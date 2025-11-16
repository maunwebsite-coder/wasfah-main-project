<?php

namespace App\Livewire\Bookings;

use App\Models\WorkshopBooking;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class WhatsappBookingVerification extends Component
{
    #[Locked]
    public int $workshopId;

    public bool $hasWhatsappBooking = false;

    public ?int $bookingId = null;

    public bool $verifying = false;

    public ?string $statusMessage = null;

    public string $statusTone = 'info';

    public string $stripeElementId = 'stripe-checkout-card';

    public function mount(
        int $workshopId,
        bool $initialHasWhatsappBooking = false,
        ?int $initialBookingId = null,
        string $stripeElementId = 'stripe-checkout-card'
    ): void {
        $this->workshopId = $workshopId;
        $this->hasWhatsappBooking = $initialHasWhatsappBooking;
        $this->bookingId = $initialBookingId;
        $this->stripeElementId = $stripeElementId ?: 'stripe-checkout-card';

        if ($this->hasWhatsappBooking) {
            $this->dispatchHideStripe();
            $this->statusMessage = __('workshops.whatsapp_verification.awaiting_confirmation');
            $this->statusTone = 'info';
        }
    }

    #[On('workshop-whatsapp-booked')]
    public function handleWhatsappBooking(?array $payload = null): void
    {
        if (!Auth::check()) {
            return;
        }

        $workshopId = (int) data_get($payload, 'workshopId');

        if ($workshopId !== $this->workshopId) {
            return;
        }

        $bookingId = data_get($payload, 'bookingId');

        $this->refreshBooking(
            $bookingId ? (int) $bookingId : null
        );
    }

    public function verifyWhatsappBooking(): void
    {
        if (!Auth::check()) {
            $this->statusMessage = __('workshops.whatsapp_verification.login_required');
            $this->statusTone = 'error';
            return;
        }

        $this->verifying = true;

        try {
            $booking = $this->locateWhatsappBooking($this->bookingId);

            if ($booking) {
                $statusKey = $booking->status === 'confirmed'
                    ? 'status_confirmed'
                    : 'status_pending';

                $statusLabel = __('workshops.whatsapp_verification.' . $statusKey);
                $updatedAt = $booking->updated_at
                    ? $booking->updated_at
                        ->copy()
                        ->timezone(config('app.timezone'))
                        ->locale(app()->getLocale())
                        ->translatedFormat('d/m/Y h:i A')
                    : __('workshops.whatsapp_verification.unknown_time');

                $this->statusMessage = __('workshops.whatsapp_verification.success', [
                    'status' => $statusLabel,
                    'updated_at' => $updatedAt,
                ]);
                $this->statusTone = 'success';
            } else {
                $this->statusMessage = __('workshops.whatsapp_verification.not_found');
                $this->statusTone = 'error';
            }
        } catch (\Throwable $exception) {
            report($exception);
            $this->statusMessage = __('workshops.whatsapp_verification.unexpected_error');
            $this->statusTone = 'error';
        } finally {
            $this->verifying = false;
        }
    }

    public function render()
    {
        return view('livewire.bookings.whatsapp-booking-verification');
    }

    protected function refreshBooking(?int $bookingId = null): void
    {
        $booking = $this->locateWhatsappBooking($bookingId);
        $this->hasWhatsappBooking = (bool) $booking;
        $this->bookingId = $booking?->id;

        if ($this->hasWhatsappBooking) {
            $this->dispatchHideStripe();

            if (!$this->statusMessage) {
                $this->statusMessage = __('workshops.whatsapp_verification.awaiting_confirmation');
                $this->statusTone = 'info';
            }
        }
    }

    protected function locateWhatsappBooking(?int $bookingId = null): ?WorkshopBooking
    {
        if (!Auth::check()) {
            return null;
        }

        $query = WorkshopBooking::query()
            ->where('workshop_id', $this->workshopId)
            ->where('user_id', Auth::id());

        if ($bookingId) {
            $query->where('id', $bookingId);
        }

        $booking = $query->latest()->first();

        if ($booking && !$booking->is_whatsapp_booking) {
            return null;
        }

        return $booking;
    }

    protected function dispatchHideStripe(): void
    {
        $this->dispatch(
            'workshop-hide-stripe',
            workshopId: $this->workshopId,
            elementId: $this->stripeElementId
        );
    }
}
