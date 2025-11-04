<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use App\Services\EnhancedImageUploadService;
use App\Services\JitsiMeetingService;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class WorkshopController extends Controller
{
    public function __construct(protected JitsiMeetingService $jitsiMeetingService)
    {
    }

    public function index(): \Illuminate\View\View
    {
        $baseQuery = Workshop::query()->where('user_id', Auth::id());

        $workshopsQuery = (clone $baseQuery)
            ->withCount([
                'bookings as confirmed_bookings' => fn ($query) => $query->where('status', 'confirmed'),
            ])
            ->orderByDesc('start_date');

        /** @var LengthAwarePaginator $workshops */
        $workshops = $workshopsQuery
            ->paginate(8)
            ->withQueryString();

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'active' => (clone $baseQuery)->where('is_active', true)->count(),
            'online' => (clone $baseQuery)->where('is_online', true)->count(),
            'drafts' => (clone $baseQuery)->where('is_active', false)->count(),
        ];

        return view('chef.workshops.index', compact('workshops', 'stats'));
    }

    public function create(): \Illuminate\View\View
    {
        return view('chef.workshops.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateWorkshop($request);

        $workshop = new Workshop();
        $this->fillWorkshopData($workshop, $data);
        $this->handleImageUpload($request, $workshop);
        $this->applyMeetingProvider($request, $workshop, $data['meeting_link'] ?? null);
        $workshop->user_id = Auth::id();
        $workshop->instructor = $data['instructor'] ?? Auth::user()->name;
        $workshop->instructor_bio = $data['instructor_bio'] ?? Auth::user()->chef_specialty_description;
        $workshop->instructor_avatar = Auth::user()->avatar;
        $workshop->is_featured = false;
        $workshop->save();

        return redirect()
            ->route('chef.workshops.index')
            ->with('success', 'تم إنشاء الورشة بنجاح! يمكنك متابعة حالة الحجوزات من لوحة التحكم.');
    }

    public function edit(Workshop $workshop): \Illuminate\View\View
    {
        $this->authorizeWorkshop($workshop);

        return view('chef.workshops.edit', compact('workshop'));
    }

    public function update(Request $request, Workshop $workshop): RedirectResponse
    {
        $this->authorizeWorkshop($workshop);

        $data = $this->validateWorkshop($request, $workshop->id);
        $this->fillWorkshopData($workshop, $data);
        $this->handleImageUpload($request, $workshop);
        $this->applyMeetingProvider($request, $workshop, $data['meeting_link'] ?? $workshop->meeting_link);
        $workshop->instructor = $data['instructor'] ?? $workshop->instructor ?? Auth::user()->name;
        $workshop->instructor_bio = $data['instructor_bio'] ?? $workshop->instructor_bio ?? Auth::user()->chef_specialty_description;
        $workshop->save();

        return redirect()
            ->route('chef.workshops.index')
            ->with('success', 'تم تحديث الورشة بنجاح!');
    }

    public function destroy(Workshop $workshop): RedirectResponse
    {
        $this->authorizeWorkshop($workshop);

        if ($workshop->image) {
            EnhancedImageUploadService::deleteImage($workshop->image);
        }

        $workshop->delete();

        return redirect()
            ->route('chef.workshops.index')
            ->with('success', 'تم حذف الورشة بنجاح.');
    }

    public function generateMeetingLink(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
        ]);

        $startsAt = isset($validated['start_date']) ? Carbon::parse($validated['start_date']) : null;

        $meeting = $this->jitsiMeetingService->createMeeting(
            $validated['title'],
            Auth::id(),
            $startsAt
        );

        return response()->json([
            'success' => true,
            'meeting_link' => $meeting['url'],
            'room' => $meeting['room'],
            'passcode' => $meeting['passcode'],
        ]);
    }

    protected function validateWorkshop(Request $request, ?int $workshopId = null): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:20'],
            'content' => ['nullable', 'string'],
            'category' => ['required', 'string', 'max:120'],
            'level' => ['required', Rule::in(['beginner', 'intermediate', 'advanced'])],
            'duration' => ['required', 'integer', 'min:30', 'max:600'],
            'max_participants' => ['required', 'integer', 'min:1', 'max:500'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', Rule::in(['JOD', 'AED', 'SAR'])],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'registration_deadline' => ['nullable', 'date', 'before:start_date'],
            'location' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'is_online' => ['required', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'instructor' => ['nullable', 'string', 'max:255'],
            'instructor_bio' => ['nullable', 'string'],
            'what_you_will_learn' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'materials_needed' => ['nullable', 'string'],
            'meeting_link' => ['nullable', 'url', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'remove_image' => ['sometimes', 'boolean'],
            'auto_generate_meeting' => ['sometimes', 'boolean'],
        ];

        $data = $request->validate($rules);

        if (!empty($data['is_online']) && empty($data['meeting_link']) && !$request->boolean('auto_generate_meeting')) {
            throw ValidationException::withMessages([
                'meeting_link' => 'يرجى إدخال رابط اجتماع أو اختيار خيار توليد رابط Jitsi تلقائياً.',
            ]);
        }

        if (empty($data['is_online']) && empty($data['location'])) {
            throw ValidationException::withMessages([
                'location' => 'يرجى تحديد موقع الورشة الحضورية.',
            ]);
        }

        return $data;
    }

    protected function fillWorkshopData(Workshop $workshop, array $data): void
    {
        $workshop->fill([
            'title' => $data['title'],
            'description' => $data['description'],
            'content' => $data['content'] ?? null,
            'category' => $data['category'],
            'level' => $data['level'],
            'duration' => $data['duration'],
            'max_participants' => $data['max_participants'],
            'price' => $data['price'],
            'currency' => $data['currency'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'registration_deadline' => $data['registration_deadline'] ?? null,
            'location' => $data['location'] ?? null,
            'address' => $data['address'] ?? null,
            'what_you_will_learn' => $data['what_you_will_learn'] ?? null,
            'requirements' => $data['requirements'] ?? null,
            'materials_needed' => $data['materials_needed'] ?? null,
            'is_online' => (bool) $data['is_online'],
            'is_active' => isset($data['is_active'])
                ? (bool) $data['is_active']
                : false,
        ]);

        if (!$workshop->is_online && empty($workshop->location)) {
            $workshop->location = 'سيتم تحديد الموقع لاحقاً';
        }
    }

    protected function handleImageUpload(Request $request, Workshop $workshop): void
    {
        if ($request->hasFile('image')) {
            if ($workshop->image) {
                EnhancedImageUploadService::deleteImage($workshop->image);
            }

            $uploadResult = EnhancedImageUploadService::uploadImage(
                $request->file('image'),
                'workshops',
                85,
                1200,
                1200
            );

            if (!$uploadResult['success']) {
                throw ValidationException::withMessages([
                    'image' => $uploadResult['error'] ?? 'تعذر رفع الصورة، يرجى المحاولة مرة أخرى.',
                ]);
            }

            $workshop->image = $uploadResult['path'];
        } elseif ($request->boolean('remove_image') && $workshop->image) {
            EnhancedImageUploadService::deleteImage($workshop->image);
            $workshop->image = null;
        }
    }

    protected function applyMeetingProvider(Request $request, Workshop $workshop, ?string $inputLink = null): void
    {
        if (!$workshop->is_online) {
            $workshop->meeting_link = null;
            $workshop->meeting_provider = 'manual';
            $workshop->jitsi_room = null;
            $workshop->jitsi_passcode = null;
            return;
        }

        $autoGenerate = $request->boolean('auto_generate_meeting');

        if ($autoGenerate || empty($inputLink)) {
            $meeting = $this->jitsiMeetingService->createMeeting(
                $workshop->title,
                Auth::id(),
                $workshop->start_date instanceof Carbon ? $workshop->start_date : Carbon::parse($workshop->start_date)
            );

            $workshop->meeting_link = $meeting['url'];
            $workshop->meeting_provider = 'jitsi';
            $workshop->jitsi_room = $meeting['room'];
            $workshop->jitsi_passcode = $meeting['passcode'];
            $workshop->location = $workshop->location ?: 'أونلاين عبر Jitsi Meet';
        } else {
            $workshop->meeting_link = $inputLink;
            $workshop->meeting_provider = 'manual';
            $workshop->jitsi_room = null;
            $workshop->jitsi_passcode = null;
        }
    }

    protected function authorizeWorkshop(Workshop $workshop): void
    {
        if ($workshop->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الورشة.');
        }
    }
}
