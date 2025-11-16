<?php

namespace App\Services;

use App\Support\Currency;

class CurrencyConversionService
{
    public function convert(float $amount, string $fromCurrency, ?string $toCurrency = null): float
    {
        return Currency::convert($amount, $fromCurrency, $toCurrency);
    }

    public function quote(string $fromCurrency, ?string $toCurrency = null): float
    {
        $toCurrency = $toCurrency ? strtoupper($toCurrency) : Currency::default();

        if (strtoupper($fromCurrency) === $toCurrency) {
            return 1.0;
        }

        $fromRate = Currency::rateToUsd($fromCurrency);
        $toRate = Currency::rateToUsd($toCurrency) ?: 1.0;

        return $toRate > 0 ? round($fromRate / $toRate, 6) : 1.0;
    }
}

