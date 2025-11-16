<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingRevenueShare;
use App\Models\FinanceInvoice;
use App\Models\WorkshopBooking;
use App\Services\FinanceInvoiceService;
use App\Support\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class FinanceController extends Controller
{
    public function __construct(protected FinanceInvoiceService $invoiceService)
    {
        $this->middleware(['auth', 'admin']);
    }

    public function dashboard(Request $request)
    {
        $currencyOptions = Currency::all();
        $selectedCurrency = strtoupper($request->get('currency', Currency::default()));

        if (! array_key_exists($selectedCurrency, $currencyOptions)) {
            $selectedCurrency = Currency::default();
        }

        $periodDays = (int) max(7, min(90, $request->integer('period', 30)));
        $periodEnd = now()->copy()->endOfDay();
        $periodStart = $periodEnd->copy()->subDays($periodDays - 1)->startOfDay();

        $paidBookings = WorkshopBooking::query()->where('payment_status', 'paid');

        $currencyBreakdown = (clone $paidBookings)
            ->selectRaw('UPPER(payment_currency) as currency_code, COUNT(*) as total_bookings, SUM(payment_amount) as total_amount, SUM(payment_amount_usd) as total_amount_usd')
            ->groupBy('payment_currency')
            ->orderByDesc('total_amount_usd')
            ->get()
            ->map(function ($row) {
                return [
                    'currency' => $row->currency_code,
                    'total_bookings' => (int) $row->total_bookings,
                    'total_amount' => (float) $row->total_amount,
                    'total_amount_usd' => (float) $row->total_amount_usd,
                ];
            });

        $overview = [
            'paid_usd' => (float) (clone $paidBookings)->sum('payment_amount_usd'),
            'pending_usd' => (float) WorkshopBooking::where('payment_status', 'pending')->sum('payment_amount_usd'),
            'refunded_usd' => (float) WorkshopBooking::where('payment_status', 'refunded')->sum('payment_amount_usd'),
        ];

        $periodCurrencyQuery = (clone $paidBookings)
            ->where('payment_currency', $selectedCurrency)
            ->whereBetween('created_at', [$periodStart, $periodEnd]);

        $periodSummary = [
            'range' => sprintf('%s — %s', $periodStart->format('Y-m-d'), $periodEnd->format('Y-m-d')),
            'currency' => $selectedCurrency,
            'currency_label' => $currencyOptions[$selectedCurrency]['label'] ?? $selectedCurrency,
            'bookings' => (clone $periodCurrencyQuery)->count(),
            'amount' => (float) (clone $periodCurrencyQuery)->sum('payment_amount'),
            'amount_usd' => (float) (clone $periodCurrencyQuery)->sum('payment_amount_usd'),
        ];

        $periodSeries = (clone $periodCurrencyQuery)
            ->selectRaw('DATE(created_at) as day_value, SUM(payment_amount) as total_amount')
            ->groupBy('day_value')
            ->orderBy('day_value')
            ->get()
            ->map(fn ($row) => [
                'day' => $row->day_value,
                'amount' => (float) $row->total_amount,
            ]);

        $invoiceCounts = FinanceInvoice::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $invoiceStats = [
            'draft' => (int) ($invoiceCounts[FinanceInvoice::STATUS_DRAFT] ?? 0),
            'issued' => (int) ($invoiceCounts[FinanceInvoice::STATUS_ISSUED] ?? 0),
            'paid' => (int) ($invoiceCounts[FinanceInvoice::STATUS_PAID] ?? 0),
            'void' => (int) ($invoiceCounts[FinanceInvoice::STATUS_VOID] ?? 0),
        ];

        $invoiceStats['total'] = array_sum($invoiceStats);

        $recentInvoices = FinanceInvoice::with(['booking.workshop', 'booking.user'])
            ->latest('updated_at')
            ->take(5)
            ->get();

        $pendingDistributions = WorkshopBooking::with(['workshop', 'user'])
            ->where('payment_status', 'paid')
            ->where('financial_status', WorkshopBooking::FINANCIAL_STATUS_PENDING)
            ->orderBy('updated_at')
            ->take(5)
            ->get();

        $recentShares = BookingRevenueShare::with(['booking.workshop', 'recipient'])
            ->where('status', BookingRevenueShare::STATUS_DISTRIBUTED)
            ->latest('distributed_at')
            ->take(5)
            ->get();

        return view('admin.finance.dashboard', [
            'currencyOptions' => $currencyOptions,
            'selectedCurrency' => $selectedCurrency,
            'overview' => $overview,
            'currencyBreakdown' => $currencyBreakdown,
            'periodSummary' => $periodSummary,
            'periodSeries' => $periodSeries,
            'invoiceStats' => $invoiceStats,
            'recentInvoices' => $recentInvoices,
            'pendingDistributions' => $pendingDistributions,
            'recentShares' => $recentShares,
            'periodDays' => $periodDays,
        ]);
    }

    public function invoices(Request $request)
    {
        $currencyOptions = Currency::all();
        $query = FinanceInvoice::with(['booking.workshop', 'booking.user', 'creator'])
            ->orderByDesc('created_at');

        $filters = [
            'status' => $request->get('status'),
            'currency' => strtoupper((string) $request->get('currency', '')),
            'search' => trim((string) $request->get('search', '')),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        if ($filters['currency']) {
            $query->where('currency', $filters['currency']);
        }

        if ($filters['search'] !== '') {
            $query->where(function ($builder) use ($filters) {
                $builder->where('invoice_number', 'like', '%' . $filters['search'] . '%')
                    ->orWhereHas('booking', function ($bookingQuery) use ($filters) {
                        $bookingQuery->where('public_code', 'like', '%' . $filters['search'] . '%');
                    });
            });
        }

        if ($filters['date_from']) {
            try {
                $dateFrom = Carbon::parse($filters['date_from'])->startOfDay();
                $query->where('created_at', '>=', $dateFrom);
            } catch (\Throwable $exception) {
                // تجاهل التاريخ غير الصحيح
            }
        }

        if ($filters['date_to']) {
            try {
                $dateTo = Carbon::parse($filters['date_to'])->endOfDay();
                $query->where('created_at', '<=', $dateTo);
            } catch (\Throwable $exception) {
                // تجاهل التاريخ غير الصحيح
            }
        }

        $invoices = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => FinanceInvoice::count(),
            'draft' => FinanceInvoice::where('status', FinanceInvoice::STATUS_DRAFT)->count(),
            'issued' => FinanceInvoice::where('status', FinanceInvoice::STATUS_ISSUED)->count(),
            'paid' => FinanceInvoice::where('status', FinanceInvoice::STATUS_PAID)->count(),
            'void' => FinanceInvoice::where('status', FinanceInvoice::STATUS_VOID)->count(),
        ];

        return view('admin.finance.invoices.index', [
            'invoices' => $invoices,
            'stats' => $stats,
            'filters' => $filters,
            'currencyOptions' => $currencyOptions,
        ]);
    }

    public function showInvoice(FinanceInvoice $invoice)
    {
        $invoice->loadMissing(['booking.workshop', 'booking.user', 'creator']);

        return view('admin.finance.invoices.show', [
            'invoice' => $invoice,
            'currencyMeta' => Currency::meta($invoice->currency),
        ]);
    }

    public function issueInvoice(FinanceInvoice $invoice)
    {
        $this->invoiceService->issue($invoice);

        return back()->with('success', 'تم إصدار الفاتورة بنجاح.');
    }

    public function markInvoicePaid(FinanceInvoice $invoice)
    {
        $this->invoiceService->markPaid($invoice);

        return back()->with('success', 'تم تحديث حالة الفاتورة إلى مدفوعة.');
    }

    public function voidInvoice(FinanceInvoice $invoice, Request $request)
    {
        $reason = trim((string) $request->get('reason', 'manual_void'));

        $this->invoiceService->void($invoice, $reason);

        return back()->with('success', 'تم إلغاء الفاتورة وحفظ سبب الإلغاء.');
    }

    public function regenerateBookingInvoice(WorkshopBooking $booking)
    {
        $invoice = $this->invoiceService->syncFromBooking($booking->load('workshop', 'user'));

        return redirect()
            ->route('admin.finance.invoices.show', $invoice)
            ->with('success', 'تم تحديث بيانات الفاتورة بناءً على الحجز.');
    }
}

