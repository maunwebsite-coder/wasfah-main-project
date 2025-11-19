<?php

namespace App\Http\Controllers;

use App\Models\ChefLinkPage;
use App\Models\User;
use App\Models\Workshop;
use App\Services\GoogleDriveService;
use App\Support\BrandAssets;
use App\Support\Concerns\ResolvesWorkshopRecordings;
use Google\Service\Drive\DriveFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ChefPublicWorkshopController extends Controller
{
    use ResolvesWorkshopRecordings;

    public function __construct(protected GoogleDriveService $googleDrive)
    {
    }

    /**
     * Cache of the users table columns to avoid repeated schema lookups.
     */
    protected ?array $userTableColumns = null;

    /**
     * Display all recorded workshops for a given chef.
     */
    public function show(string $username): View
    {
        $chef = $this->resolveChef($username);

        if (! $chef->isChef()) {
            abort(404);
        }

        $workshops = $chef->workshops()
            ->orderByDesc('start_date')
            ->get()
            ->map(function (Workshop $workshop) {
                $recordingUrl = $this->resolveRecordingUrl($workshop);
                $previewUrl = $this->buildRecordingPreviewUrl($recordingUrl);
                $isDirectVideo = $this->isDirectVideoUrl($recordingUrl);

                $workshop->setAttribute('recording_source_url', $recordingUrl);
                $workshop->setAttribute('video_preview_url', $previewUrl);
                $workshop->setAttribute('is_direct_video', $isDirectVideo);
                $workshop->setAttribute('formatted_start_date', $this->formatWorkshopDate($workshop));

                return $workshop;
            });

        $appLocale = app()->getLocale();
        $carbonLocale = $appLocale === 'ar' ? 'ar' : 'en';
        $dateTimeFormat = __('chef.workshops.datetime_format');
        $driveRecordings = $this->resolveDriveRecordings($workshops, $carbonLocale, $dateTimeFormat);
        $workshops = $this->attachDriveRecordingsToWorkshops($workshops, $driveRecordings);

        return view('chef.public-workshops', [
            'chef' => $chef,
            'avatarUrl' => $this->resolveAvatarUrl($chef->avatar),
            'workshops' => $workshops,
            'driveRecordings' => $driveRecordings,
        ]);
    }

    /**
     * Build a normalized list of Drive recordings so the view can render them.
     */
    protected function resolveDriveRecordings(Collection $workshops, string $locale, string $dateTimeFormat): Collection
    {
        if (! $this->googleDrive->isEnabled()) {
            return collect();
        }

        $meetingCodeVariants = $this->buildMeetingCodeVariantMap($workshops);

        if (empty($meetingCodeVariants)) {
            return collect();
        }

        return collect($this->googleDrive->listRecordings(null, 100))
            ->filter(fn ($file) => $file instanceof DriveFile)
            ->map(function (DriveFile $file) use ($meetingCodeVariants, $locale, $dateTimeFormat): ?array {
                $name = strtolower($file->getName() ?? '');

                if ($name === '') {
                    return null;
                }

                $matchedCode = $this->matchMeetingCodeFromName($name, $meetingCodeVariants);

                if (! $matchedCode) {
                    return null;
                }

                $modifiedAt = $file->getModifiedTime()
                    ? Carbon::parse($file->getModifiedTime())->locale($locale)
                    : null;

                $fileId = $file->getId();
                $previewUrl = $fileId
                    ? sprintf('https://drive.google.com/file/d/%s/preview', $fileId)
                    : null;

                $watchUrl = $file->getWebViewLink()
                    ?: ($previewUrl ?: $file->getWebContentLink());

                return [
                    'id' => $fileId ?: uniqid('drive-', true),
                    'matched_code' => $matchedCode,
                    'title' => $file->getName() ?: __('chef.recordings.untitled'),
                    'description' => $file->getDescription()
                        ? Str::limit($file->getDescription(), 140)
                        : __('chef.recordings.drive_default_description'),
                    'modified_label' => $modifiedAt
                        ? $modifiedAt->translatedFormat($dateTimeFormat)
                        : __('chef.recordings.updated_unknown'),
                    'preview_url' => $previewUrl,
                    'watch_url' => $watchUrl,
                ];
            })
            ->filter()
            ->values();
    }

    /**
     * Attach any resolved Drive recordings back onto their workshops for inline playback.
     */
    protected function attachDriveRecordingsToWorkshops(Collection $workshops, Collection $driveRecordings): Collection
    {
        if ($driveRecordings->isEmpty()) {
            return $workshops;
        }

        $recordingsByCode = $driveRecordings
            ->filter(fn (array $recording) => ! empty($recording['matched_code']))
            ->groupBy('matched_code');

        if ($recordingsByCode->isEmpty()) {
            return $workshops;
        }

        return $workshops->map(function (Workshop $workshop) use ($recordingsByCode) {
            $normalizedCode = $this->normalizeMeetingCodeValue($workshop->meeting_code);

            if (! $normalizedCode || ! $recordingsByCode->has($normalizedCode)) {
                return $workshop;
            }

            $recording = $recordingsByCode->get($normalizedCode)->first();

            if (! $recording) {
                return $workshop;
            }

            $currentSource = $workshop->getAttribute('recording_source_url');
            $currentPreview = $workshop->getAttribute('video_preview_url');

            if (! $currentSource && ! empty($recording['watch_url'])) {
                $workshop->setAttribute('recording_source_url', $recording['watch_url']);
                $currentSource = $recording['watch_url'];
            }

            if (! $currentPreview && ! empty($recording['preview_url'])) {
                $workshop->setAttribute('video_preview_url', $recording['preview_url']);
            }

            if (! $workshop->getAttribute('is_direct_video')) {
                $workshop->setAttribute(
                    'is_direct_video',
                    $this->isDirectVideoUrl($workshop->getAttribute('recording_source_url'))
                );
            }

            if (! empty($recording['preview_url']) || ! empty($recording['watch_url'])) {
                $workshop->setAttribute('recording_from_drive', true);
            }

            return $workshop;
        });
    }

    /**
     * Build a lookup table of meeting code variants mapped to their canonical forms.
     */
    protected function buildMeetingCodeVariantMap(Collection $workshops): array
    {
        $variantMap = [];

        foreach ($workshops as $workshop) {
            $normalized = $this->normalizeMeetingCodeValue($workshop->meeting_code);

            if (! $normalized) {
                continue;
            }

            foreach ($this->expandMeetingCodeVariants($normalized) as $variant) {
                if ($variant === '') {
                    continue;
                }

                $variantMap[$variant] = $normalized;
            }
        }

        uksort($variantMap, fn ($a, $b) => strlen($b) <=> strlen($a));

        return $variantMap;
    }

    /**
     * Determine which meeting code variant appears in the provided file name.
     */
    protected function matchMeetingCodeFromName(string $fileName, array $variantMap): ?string
    {
        foreach ($variantMap as $variant => $canonical) {
            if ($variant !== '' && str_contains($fileName, $variant)) {
                return $canonical;
            }
        }

        return null;
    }

    /**
     * Normalize meeting codes to make filtering Drive file names easier.
     */
    protected function normalizeMeetingCodeValue(?string $code): ?string
    {
        if (! is_string($code)) {
            return null;
        }

        $normalized = strtolower(trim($code));

        return $normalized !== '' ? $normalized : null;
    }

    /**
     * Generate the common variants of a meeting code that may appear in Drive file names.
     */
    protected function expandMeetingCodeVariants(string $code): array
    {
        $variants = [$code];

        $compressed = str_replace([' ', '-', '_'], '', $code);
        if ($compressed !== $code) {
            $variants[] = $compressed;
        }

        $hyphenated = str_replace([' ', '_'], '-', $code);
        if ($hyphenated !== $code) {
            $variants[] = $hyphenated;
        }

        $spaced = str_replace(['-', '_'], ' ', $code);
        if ($spaced !== $code) {
            $variants[] = $spaced;
        }

        return array_values(array_unique(array_filter($variants)));
    }

    /**
     * Resolve the chef model from the provided identifier.
     */
    protected function resolveChef(string $identifier): User
    {
        if ($user = $this->findChefByColumn($identifier)) {
            return $user;
        }

        $sluggedIdentifier = Str::slug($identifier, '-', app()->getLocale() ?? 'en');

        if ($sluggedIdentifier !== '') {
            $user = User::query()
                ->whereRaw('LOWER(REPLACE(name, " ", "-")) = ?', [$sluggedIdentifier])
                ->first();

            if ($user) {
                return $user;
            }

            $user = User::query()
                ->where('role', User::ROLE_CHEF)
                ->get()
                ->first(function (User $candidate) use ($sluggedIdentifier) {
                    return Str::slug((string) $candidate->name, '-') === $sluggedIdentifier;
                });

            if ($user) {
                return $user;
            }
        }

        if (is_numeric($identifier)) {
            $user = User::query()->find((int) $identifier);
            if ($user) {
                return $user;
            }
        }

        $linkPage = ChefLinkPage::query()
            ->where('slug', $identifier)
            ->first();

        if ($linkPage && $linkPage->user) {
            return $linkPage->user;
        }

        abort(404);
    }

    /**
     * Attempt to find a chef using commonly used username/slug columns.
     */
    protected function findChefByColumn(string $identifier): ?User
    {
        $candidateColumns = array_filter([
            $this->usersTableHasColumn('username') ? 'username' : null,
            $this->usersTableHasColumn('slug') ? 'slug' : null,
            $this->usersTableHasColumn('handle') ? 'handle' : null,
            $this->usersTableHasColumn('referral_code') ? 'referral_code' : null,
        ]);

        foreach ($candidateColumns as $column) {
            $user = User::query()
                ->where($column, $identifier)
                ->first();

            if ($user) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Determine whether the users table has the given column.
     */
    protected function usersTableHasColumn(string $column): bool
    {
        if ($this->userTableColumns === null) {
            $this->userTableColumns = Schema::getColumnListing('users');
        }

        return in_array($column, $this->userTableColumns, true);
    }

    /**
     * Resolve the public avatar URL for the chef.
     */
    protected function resolveAvatarUrl(?string $avatar): string
    {
        if (! $avatar) {
            return BrandAssets::logoAsset('webp');
        }

        if (str_starts_with($avatar, 'http://') || str_starts_with($avatar, 'https://')) {
            return $avatar;
        }

        if (Storage::disk('public')->exists($avatar)) {
            return Storage::disk('public')->url($avatar);
        }

        return asset(trim($avatar, '/'));
    }
}
