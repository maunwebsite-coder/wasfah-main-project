<?php

namespace App\Services;

use App\Models\FinanceInvoice;
use App\Models\WorkshopBooking;
use App\Support\Currency;
use Illuminate\Support\Facades\Auth;

class FinanceInvoiceService
{
    public function syncFromBooking(WorkshopBooking $booking): FinanceInvoice
    {
        $booking->loadMissing('workshop', 'user');

        $invoice = FinanceInvoice::firstOrNew([
            'workshop_booking_id' => $booking->id,
        ]);

        if (! $invoice->exists) {
            $invoice->invoice_number = $this->generateInvoiceNumber();
            $invoice->type = FinanceInvoice::TYPE_BOOKING;
            $invoice->created_by = Auth::id();
        }

        $currency = strtoupper($booking->payment_currency ?: ($booking->workshop->currency ?? Currency::default()));
        $lineItems = $this->buildBookingLineItems($booking, $currency);

        $invoice->fill([
            'currency' => $currency,
            'subtotal' => $lineItems['subtotal'],
            'tax_amount' => $lineItems['tax_amount'],
            'total' => $lineItems['total'],
            'line_items' => $lineItems['items'],
            'meta' => array_merge($invoice->meta ?? [], [
                'workshop_title' => $booking->workshop->title ?? null,
                'customer_name' => optional($booking->user)->name,
                'payment_method' => $booking->payment_method,
                'booking_public_code' => $booking->public_code,
                'exchange_rate' => $booking->payment_exchange_rate,
            ]),
        ]);

        $this->applyStatusFromBooking($invoice, $booking);

        $invoice->save();

        return $invoice;
    }

    public function issue(FinanceInvoice $invoice): FinanceInvoice
    {
        if ($invoice->status === FinanceInvoice::STATUS_VOID) {
            return $invoice;
        }

        $invoice->status = FinanceInvoice::STATUS_ISSUED;
        $invoice->issued_at = $invoice->issued_at ?? now();
        $invoice->save();

        return $invoice;
    }

    public function markPaid(FinanceInvoice $invoice): FinanceInvoice
    {
        $invoice->status = FinanceInvoice::STATUS_PAID;
        $invoice->issued_at = $invoice->issued_at ?? now();
        $invoice->paid_at = now();
        $invoice->voided_at = null;
        $invoice->save();

        return $invoice;
    }

    public function void(FinanceInvoice $invoice, ?string $reason = null): FinanceInvoice
    {
        $invoice->status = FinanceInvoice::STATUS_VOID;
        $invoice->voided_at = now();
        $meta = $invoice->meta ?? [];

        if ($reason) {
            $meta['void_reason'] = $reason;
        }

        $invoice->meta = $meta;
        $invoice->save();

        return $invoice;
    }

    protected function buildBookingLineItems(WorkshopBooking $booking, string $currency): array
    {
        $amount = (float) $booking->payment_amount;
        $taxRate = (float) config('tax.default_rate', 0);
        $taxAmount = $taxRate > 0
            ? Currency::round($amount * ($taxRate / 100), $currency)
            : 0.0;
        $total = Currency::round($amount + $taxAmount, $currency);

        return [
            'items' => [[
                'description' => 'حجز ورشة: ' . ($booking->workshop->title ?? '#'.$booking->workshop_id),
                'quantity' => 1,
                'unit_price' => $amount,
                'total' => $amount,
                'currency' => $currency,
            ]],
            'subtotal' => $amount,
            'tax_amount' => $taxAmount,
            'total' => $total,
        ];
    }

    protected function applyStatusFromBooking(FinanceInvoice $invoice, WorkshopBooking $booking): void
    {
        if ($booking->payment_status === 'paid') {
            $invoice->status = FinanceInvoice::STATUS_PAID;
            $invoice->issued_at = $invoice->issued_at ?? now();
            $invoice->paid_at = now();
            $invoice->voided_at = null;

            return;
        }

        if ($booking->payment_status === 'pending') {
            $invoice->status = FinanceInvoice::STATUS_DRAFT;
            $invoice->paid_at = null;
            $invoice->voided_at = null;

            return;
        }

        $invoice->status = FinanceInvoice::STATUS_VOID;
        $invoice->voided_at = now();
        $invoice->paid_at = null;
    }

    protected function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . now()->format('Ym');

        do {
            $sequence = str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
            $number = "{$prefix}-{$sequence}";
        } while (FinanceInvoice::where('invoice_number', $number)->exists());

        return $number;
    }
}
