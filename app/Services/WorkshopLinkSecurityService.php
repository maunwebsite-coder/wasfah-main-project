<?php

namespace App\Services;

use App\Models\Workshop;
use App\Models\WorkshopBooking;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Carbon;

class WorkshopLinkSecurityService
{
    public function __construct(
        protected UrlGenerator $urlGenerator,
        protected ConfigRepository $config,
    ) {
    }

    public function makeParticipantJoinUrl(WorkshopBooking $booking, ?Carbon $expiresAt = null): string
    {
        $expires = $expiresAt ?? $this->expiryFromConfig('participant_join_ttl');

        return $this->urlGenerator->temporarySignedRoute(
            'bookings.join',
            $expires,
            ['booking' => $booking->public_code],
        );
    }

    public function makeParticipantStatusUrl(WorkshopBooking $booking, ?Carbon $expiresAt = null): string
    {
        $expires = $expiresAt ?? $this->expiryFromConfig('participant_status_ttl');

        return $this->urlGenerator->temporarySignedRoute(
            'bookings.status',
            $expires,
            ['booking' => $booking->public_code],
        );
    }

    public function makeParticipantMobileUrl(WorkshopBooking $booking, ?Carbon $expiresAt = null): string
    {
        $expires = $expiresAt ?? $this->expiryFromConfig('participant_mobile_ttl');

        return $this->urlGenerator->temporarySignedRoute(
            'bookings.mobile-entry',
            $expires,
            ['booking' => $booking->public_code],
        );
    }

    protected function expiryFromConfig(string $key): Carbon
    {
        $minutes = (int) $this->config->get("workshop-links.{$key}", 60);

        if ($minutes <= 0) {
            $minutes = 60;
        }

        return now()->addMinutes($minutes);
    }
}
