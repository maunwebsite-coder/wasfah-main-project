<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Workshop;
use App\Models\WorkshopBooking;
use App\Services\Payments\StripeClient;
use App\Support\NotificationCopy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class StripeBookingController extends Controller
{
    public function __construct(
        protected StripeClient $client,
    ) {
        $this->middleware('auth');
    }

    public function createIntent(Request $request): JsonResponse
    {
        $this->ensureGatewayIsReady();

        $data = $request->validate([
            'workshop_id' => ['required', 'exists:workshops,id'],
        ]);

        $user = $request->user();
        $workshop = Workshop::active()->findOrFail($data['workshop_id']);

        $this->ensureWorkshopIsBookable($workshop);
        $this->ensurePriceAllowsOnlinePayment($workshop);

        $existingBooking = WorkshopBooking::where('workshop_id', $workshop->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingBooking && $existingBooking->status === 'confirmed' && $existingBooking->payment_status === 'paid') {
            throw ValidationException::withMessages([
                'workshop_id' => __('لقد أكملت حجز هذه الورشة بالفعل.'),
            ]);
        }

        try {
            $intent = $this->client->createPaymentIntent(
                amount: (float) $workshop->price,
                currency: $workshop->currency ?? config('finance.default_currency', 'USD'),
                metadata: [
                    'reference_id' => 'workshop_' . $workshop->id,
                    'user_id' => (string) $user->id,
                    'workshop_id' => (string) $workshop->id,
                    'description' => sprintf('حجز ورشة: %s', $workshop->title),
                ],
            );
        } catch (Throwable $exception) {
            Log::error('Failed to create Stripe payment intent.', [
                'message' => $exception->getMessage(),
                'user_id' => $user->id,
                'workshop_id' => $workshop->id,
            ]);

            return response()->json([
                'message' => 'تعذر تجهيز عملية الدفع. يرجى المحاولة لاحقاً.',
            ], 422);
        }

        return response()->json([
            'client_secret' => $intent['client_secret'] ?? null,
            'payment_intent_id' => $intent['id'] ?? null,
            'publishable_key' => $this->client->getPublicKey(),
            'amount' => $intent['amount'] ?? null,
            'currency' => isset($intent['currency']) ? strtoupper($intent['currency']) : null,
            'zero_decimal_currency' => isset($intent['currency'])
                ? $this->client->isZeroDecimalCurrency($intent['currency'])
                : null,
        ]);
    }

    public function confirm(Request $request): JsonResponse
    {
        $this->ensureGatewayIsReady();

        $data = $request->validate([
            'workshop_id' => ['required', 'exists:workshops,id'],
            'payment_intent_id' => ['required', 'string'],
        ]);

        $user = $request->user();
        $workshop = Workshop::active()->findOrFail($data['workshop_id']);

        $this->ensureWorkshopIsBookable($workshop);
        $this->ensurePriceAllowsOnlinePayment($workshop);

        $existingBooking = WorkshopBooking::where('workshop_id', $workshop->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingBooking && $existingBooking->status === 'confirmed' && $existingBooking->payment_status === 'paid') {
            return $this->successResponse($existingBooking, 'تم تأكيد حجزك مسبقاً.');
        }

        try {
            $intent = $this->client->retrievePaymentIntent($data['payment_intent_id']);
        } catch (Throwable $exception) {
            Log::error('Failed to retrieve Stripe payment intent.', [
                'message' => $exception->getMessage(),
                'user_id' => $user->id,
                'workshop_id' => $workshop->id,
                'payment_intent_id' => $data['payment_intent_id'],
            ]);

            return response()->json([
                'message' => 'تعذر التحقق من عملية الدفع. يرجى المحاولة مرة أخرى.',
            ], 422);
        }

        $status = $intent['status'] ?? null;

        if ($status !== 'succeeded') {
            return response()->json([
                'message' => 'لم يتم تأكيد عملية الدفع بعد. يرجى المحاولة مجدداً.',
            ], 422);
        }

        $currency = strtoupper($intent['currency'] ?? ($workshop->currency ?? config('finance.default_currency', 'USD')));
        $amountReceived = $intent['amount_received'] ?? $intent['amount'];

        if ($amountReceived === null) {
            Log::warning('Stripe payment intent missing amount.', [
                'payment_intent_id' => $data['payment_intent_id'],
            ]);

            return response()->json([
                'message' => 'لم نتمكن من التحقق من قيمة الدفع. تواصل مع الدعم.',
            ], 422);
        }

        $capturedAmount = $this->client->normalizeAmountFromStripe($amountReceived, $currency);
        $expectedAmount = (float) $workshop->price;

        if ($expectedAmount > 0 && abs($capturedAmount - $expectedAmount) > 0.49) {
            Log::warning('Stripe amount mismatch.', [
                'payment_intent_id' => $data['payment_intent_id'],
                'expected' => $expectedAmount,
                'captured' => $capturedAmount,
            ]);

            return response()->json([
                'message' => 'قيمة الدفع لا تطابق سعر الورشة. تم إلغاء العملية تلقائياً.',
            ], 422);
        }

        $booking = DB::transaction(function () use ($existingBooking, $user, $workshop, $intent, $capturedAmount, $currency) {
            $booking = $existingBooking;

            if (! $booking) {
                $booking = WorkshopBooking::create([
                    'workshop_id' => $workshop->id,
                    'user_id' => $user->id,
                    'status' => 'pending',
                    'booking_date' => now(),
                    'payment_status' => 'pending',
                    'payment_method' => 'stripe',
                    'payment_amount' => $capturedAmount,
                    'payment_currency' => $currency,
                    'notes' => 'تم إنشاء الحجز بعد الدفع الإلكتروني.',
                ]);
            }

            $booking->forceFill([
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'payment_method' => 'stripe',
                'payment_reference' => $intent['id'] ?? null,
                'payment_payload' => $intent,
                'payment_amount' => $capturedAmount,
                'payment_currency' => $currency,
                'confirmed_at' => now(),
                'booking_date' => $booking->booking_date ?? now(),
                'notes' => $booking->notes ?: 'تم الدفع إلكترونياً عبر Stripe.',
            ])->save();

            return $booking->fresh();
        });

        $this->notifyParticipant($booking, $workshop);

        return $this->successResponse($booking, 'تم تأكيد حجزك بنجاح بعد الدفع عبر Stripe.');
    }

    protected function ensureGatewayIsReady(): void
    {
        if (! $this->client->isEnabled()) {
            abort(503, 'الدفع عبر Stripe غير مفعل حالياً.');
        }
    }

    /**
     * @throws ValidationException
     */
    protected function ensureWorkshopIsBookable(Workshop $workshop): void
    {
        if (! $workshop->is_active) {
            throw ValidationException::withMessages([
                'workshop_id' => 'هذه الورشة غير متاحة للحجز حالياً.',
            ]);
        }

        if ($workshop->is_completed) {
            throw ValidationException::withMessages([
                'workshop_id' => 'انتهت هذه الورشة بالفعل.',
            ]);
        }

        if ($workshop->is_fully_booked) {
            throw ValidationException::withMessages([
                'workshop_id' => 'عذراً، تم اكتمال العدد في هذه الورشة.',
            ]);
        }

        if (! $workshop->is_registration_open) {
            throw ValidationException::withMessages([
                'workshop_id' => 'انتهى موعد التسجيل لهذه الورشة.',
            ]);
        }
    }

    /**
     * @throws ValidationException
     */
    protected function ensurePriceAllowsOnlinePayment(Workshop $workshop): void
    {
        if ((float) $workshop->price <= 0) {
            throw ValidationException::withMessages([
                'workshop_id' => 'لا يمكن تفعيل الدفع الإلكتروني لورشة مجانية.',
            ]);
        }
    }

    protected function notifyParticipant(WorkshopBooking $booking, Workshop $workshop): void
    {
        [$title, $message] = NotificationCopy::bookingConfirmed($booking, $workshop);

        Notification::createNotification(
            $booking->user_id,
            'workshop_confirmed',
            $title,
            $message,
            [
                'workshop_id' => $workshop->id,
                'workshop_slug' => $workshop->slug,
                'booking_id' => $booking->id,
                'action_url' => route('bookings.show', $booking),
            ]
        );
    }

    protected function successResponse(WorkshopBooking $booking, string $message): JsonResponse
    {
        $booking->loadMissing('workshop');
        $workshop = $booking->workshop;

        $joinUrl = null;

        if ($workshop && $workshop->is_online && $workshop->meeting_link && $booking->public_code) {
            $joinUrl = $booking->secure_join_url;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'booking_id' => $booking->id,
            'redirect_url' => route('bookings.show', $booking),
            'join_url' => $joinUrl,
        ]);
    }
}
