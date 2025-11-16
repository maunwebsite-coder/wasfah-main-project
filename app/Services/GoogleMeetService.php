<?php

namespace App\Services;

use Carbon\CarbonInterface;
use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use Google\Service\Calendar\EventAttendee;
use Google\Service\Calendar\EventOrganizer;
use Google\Service\Calendar\EventCreator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class GoogleMeetService
{
    protected ?Calendar $calendar = null;
    protected ?string $calendarId = null;
    protected int $defaultDuration;
    protected string $timezone;
    protected ?string $organizerEmail;
    protected bool $enabled = false;

    public function __construct(?Client $client = null)
    {
        $config = config('services.google_meet', []);

        $clientId = $config['client_id'] ?? null;
        $clientSecret = $config['client_secret'] ?? null;
        $refreshToken = $config['refresh_token'] ?? null;
        $calendarId = $config['calendar_id'] ?? null;
        $this->organizerEmail = $config['organizer_email'] ?? null;
        $this->defaultDuration = max(30, (int) ($config['default_duration'] ?? 90));
        $this->timezone = $config['timezone'] ?? config('app.timezone', 'UTC');

        $credentialsReady = $clientId && $clientSecret && $refreshToken;
        $calendarReady = $calendarId || $this->organizerEmail;

        if ($credentialsReady && $calendarReady) {
            $this->enabled = true;
            $this->calendarId = $calendarId ?: $this->organizerEmail;
            $this->calendar = $this->bootstrapCalendarClient($client, $clientId, $clientSecret, $refreshToken);
        }
    }

    public function createMeeting(
        string $title,
        int $userId = 0,
        ?CarbonInterface $startsAt = null,
        ?int $durationMinutes = null,
        ?string $description = null,
        array $attendees = [],
        ?array $organizerOverride = null
    ): array {
        if (!$this->enabled || !$this->calendar || !$this->calendarId) {
            throw new RuntimeException('Google Meet integration is not configured.');
        }

        $start = $startsAt?->copy() ?? now();
        $start = $start->setTimezone($this->timezone);
        $duration = max(15, (int) ($durationMinutes ?? $this->defaultDuration));
        $end = $start->copy()->addMinutes($duration);

        $eventPayload = [
            'summary' => trim($title) !== '' ? $title : 'Wasfah Online Workshop',
            'description' => $description ?: sprintf('Auto-generated for Wasfah workshop #%s', $userId ?: 'N/A'),
            'start' => $this->makeEventDateTime($start),
            'end' => $this->makeEventDateTime($end),
            'conferenceData' => [
                'createRequest' => [
                    'requestId' => (string) Str::uuid(),
                    'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
                ],
            ],
        ];

        $event = new Event($eventPayload);

        $attendeePayload = [];

        if ($this->organizerEmail) {
            $attendeePayload[$this->organizerEmail] = [
                'email' => $this->organizerEmail,
                'organizer' => true,
            ];
        }

        foreach ($attendees as $attendee) {
            if (!is_array($attendee)) {
                continue;
            }

            $email = isset($attendee['email']) ? trim((string) $attendee['email']) : '';

            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            $payload = [
                'email' => strtolower($email),
            ];

            if (!empty($attendee['displayName'])) {
                $payload['displayName'] = (string) $attendee['displayName'];
            }

            if (array_key_exists('organizer', $attendee)) {
                $payload['organizer'] = (bool) $attendee['organizer'];
            }

            $attendeePayload[$payload['email']] = $payload;
        }

        if (!empty($attendeePayload)) {
            $event->setAttendees(array_values($attendeePayload));
        }

        if ($organizerOverride) {
            $organizerEmail = isset($organizerOverride['email'])
                ? strtolower(trim((string) $organizerOverride['email']))
                : null;

            if ($organizerEmail && filter_var($organizerEmail, FILTER_VALIDATE_EMAIL)) {
                $displayName = $organizerOverride['displayName'] ?? null;

                $organizer = new EventOrganizer();
                $organizer->setEmail($organizerEmail);
                if ($displayName) {
                    $organizer->setDisplayName($displayName);
                }
                $event->setOrganizer($organizer);

                $creator = new EventCreator();
                $creator->setEmail($organizerEmail);
                if ($displayName) {
                    $creator->setDisplayName($displayName);
                }
                $event->setCreator($creator);
            }
        }

        try {
            $created = $this->calendar->events->insert(
                $this->calendarId,
                $event,
                ['conferenceDataVersion' => 1, 'sendUpdates' => 'none']
            );
        } catch (\Throwable $exception) {
            Log::error('Failed to create Google Meet event.', [
                'error' => $exception->getMessage(),
            ]);

            throw new RuntimeException('تعذر إنشاء اجتماع Google Meet. يرجى التحقق من الإعدادات وإعادة المحاولة.');
        }

        $hangoutLink = $created->getHangoutLink();

        if (!$hangoutLink) {
            $conferenceData = $created->getConferenceData();
            if ($conferenceData) {
                $entryPoints = $conferenceData->getEntryPoints() ?? [];
                if (is_array($entryPoints) && isset($entryPoints[0])) {
                    $hangoutLink = $entryPoints[0]->getUri();
                }
            }
        }

        if (!$hangoutLink) {
            throw new RuntimeException('لم يتمكن Google من إنشاء رابط اجتماع صالح.');
        }

        return [
            'meeting_link' => $hangoutLink,
            'event_id' => $created->getId(),
            'calendar_id' => $this->calendarId,
            'conference_id' => optional($created->getConferenceData())->getConferenceId(),
            'provider' => 'google_meet',
            'starts_at' => $start,
            'ends_at' => $end,
        ];
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Replace the attendee list for an existing Google Calendar event.
     *
     * @param  string  $eventId
     * @param  array<array{email:string,displayName?:string,optional?:bool,organizer?:bool}>  $attendees
     * @param  string|null  $calendarId
     */
    public function syncEventAttendees(string $eventId, array $attendees, ?string $calendarId = null): void
    {
        if (!$this->enabled || !$this->calendar) {
            throw new RuntimeException('Google Meet integration is not configured.');
        }

        $eventId = trim($eventId);

        if ($eventId === '') {
            throw new RuntimeException('لا يمكن تحديث ضيوف Google Meet بدون معرف اجتماع صالح.');
        }

        $targetCalendar = $calendarId ?: $this->calendarId;

        if (!$targetCalendar) {
            throw new RuntimeException('لا يوجد تقويم Google صالح لإرسال تحديثات الضيوف.');
        }

        $attendeeObjects = $this->buildEventAttendees($attendees);
        $event = new Event();
        $event->setAttendees($attendeeObjects);

        $this->calendar->events->patch(
            $targetCalendar,
            $eventId,
            $event,
            [
                'conferenceDataVersion' => 1,
                'sendUpdates' => 'none',
            ]
        );
    }

    protected function bootstrapCalendarClient(
        ?Client $client,
        string $clientId,
        string $clientSecret,
        string $refreshToken
    ): Calendar {
        $googleClient = $client ?: new Client();
        $googleClient->setApplicationName(config('app.name') . ' Workshops');
        $googleClient->setClientId($clientId);
        $googleClient->setClientSecret($clientSecret);
        $googleClient->setAccessType('offline');
        $googleClient->setPrompt('consent');
        $googleClient->setIncludeGrantedScopes(true);
        $googleClient->setScopes([
            Calendar::CALENDAR_EVENTS,
        ]);

        $token = $googleClient->fetchAccessTokenWithRefreshToken($refreshToken);

        if (isset($token['error'])) {
            throw new RuntimeException('فشل تحديث صلاحيات Google: ' . ($token['error_description'] ?? $token['error']));
        }

        return new Calendar($googleClient);
    }

    protected function makeEventDateTime(CarbonInterface $time): EventDateTime
    {
        $dateTime = new EventDateTime();
        $dateTime->setDateTime($time->toRfc3339String());
        $dateTime->setTimeZone($this->timezone);

        return $dateTime;
    }

    /**
     * @param  array<array{email?:string,displayName?:string,name?:string,optional?:bool,organizer?:bool}>  $attendees
     * @return EventAttendee[]
     */
    protected function buildEventAttendees(array $attendees): array
    {
        $result = [];

        foreach ($attendees as $attendee) {
            $email = isset($attendee['email']) ? trim((string) $attendee['email']) : '';

            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            $payload = [
                'email' => $email,
            ];

            $displayName = $attendee['displayName'] ?? $attendee['name'] ?? null;

            if (is_string($displayName) && trim($displayName) !== '') {
                $payload['displayName'] = trim($displayName);
            }

            if (isset($attendee['optional'])) {
                $payload['optional'] = (bool) $attendee['optional'];
            }

            if (isset($attendee['organizer'])) {
                $payload['organizer'] = (bool) $attendee['organizer'];
            }

            $result[] = new EventAttendee($payload);
        }

        return $result;
    }

    public function eventHasAttendee(string $eventId, string $email, ?string $calendarId = null): ?bool
    {
        if (!$this->enabled || !$this->calendar) {
            return null;
        }

        $targetCalendar = $calendarId ?: $this->calendarId;

        if (!$targetCalendar) {
            return null;
        }

        try {
            $event = $this->calendar->events->get($targetCalendar, $eventId);
        } catch (\Throwable $exception) {
            Log::warning('Failed to fetch Google Meet event for attendee verification.', [
                'event_id' => $eventId,
                'calendar_id' => $targetCalendar,
                'error' => $exception->getMessage(),
            ]);

            return null;
        }

        $attendees = $event->getAttendees() ?? [];

        foreach ($attendees as $attendee) {
            $attendeeEmail = strtolower((string) $attendee->getEmail());

            if ($attendeeEmail !== '' && hash_equals($attendeeEmail, strtolower($email))) {
                return true;
            }
        }

        return false;
    }

    public function ensureAttendeePresent(
        string $eventId,
        array $attendee,
        ?string $calendarId = null
    ): bool {
        if (!$this->enabled || !$this->calendar) {
            return false;
        }

        $targetCalendar = $calendarId ?: $this->calendarId;

        if (!$targetCalendar) {
            return false;
        }

        try {
            $event = $this->calendar->events->get($targetCalendar, $eventId);
        } catch (\Throwable $exception) {
            Log::warning('Failed to fetch Google Meet event while ensuring attendee.', [
                'event_id' => $eventId,
                'calendar_id' => $targetCalendar,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }

        $attendees = $event->getAttendees() ?? [];
        $normalizedEmail = strtolower(trim((string) ($attendee['email'] ?? '')));

        if ($normalizedEmail === '' || !filter_var($normalizedEmail, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        foreach ($attendees as $existing) {
            $existingEmail = strtolower((string) $existing->getEmail());

            if ($existingEmail !== '' && hash_equals($existingEmail, $normalizedEmail)) {
                return true;
            }
        }

        $newAttendees = $this->buildEventAttendees([
            [
                'email' => $normalizedEmail,
                'displayName' => $attendee['displayName'] ?? null,
                'organizer' => $attendee['organizer'] ?? false,
            ],
        ]);

        if (empty($newAttendees)) {
            return false;
        }

        $attendees[] = $newAttendees[0];
        $event->setAttendees($attendees);

        try {
            $this->calendar->events->patch(
                $targetCalendar,
                $eventId,
                $event,
                [
                    'conferenceDataVersion' => 1,
                    'sendUpdates' => 'none',
                ]
            );
        } catch (\Throwable $exception) {
            Log::warning('Failed to append host attendee to Google Meet event.', [
                'event_id' => $eventId,
                'calendar_id' => $targetCalendar,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }

        return true;
    }
}
