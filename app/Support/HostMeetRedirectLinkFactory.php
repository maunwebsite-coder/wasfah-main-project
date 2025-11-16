<?php

namespace App\Support;

use App\Models\Workshop;

class HostMeetRedirectLinkFactory
{
    public static function make(Workshop $workshop): ?string
    {
        $meetingLink = $workshop->meeting_link;
        $hostEmail = $workshop->hostGoogleEmail();

        if (!$meetingLink || !$hostEmail) {
            return null;
        }

        if (!filter_var($meetingLink, FILTER_VALIDATE_URL)) {
            return null;
        }

        $payload = [
            'workshop_id' => $workshop->id,
            'chef_id' => $workshop->user_id,
            'chef_email' => $hostEmail,
            'meet_link' => $meetingLink,
            'meeting_event_id' => $workshop->meeting_event_id,
            'calendar_id' => $workshop->meeting_calendar_id,
            'expires_at' => now()
                ->addMinutes(config('services.google_meet.host_redirect_ttl', 120))
                ->getTimestamp(),
        ];

        return route('meet.redirect', ['token' => encrypt($payload)]);
    }
}
