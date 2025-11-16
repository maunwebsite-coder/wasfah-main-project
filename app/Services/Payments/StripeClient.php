<?php

namespace App\Services\Payments;

use RuntimeException;
use Stripe\StripeClient as StripeSdk;

class StripeClient
{
    protected ?string $publicKey;
    protected ?string $secretKey;
    protected ?StripeSdk $client = null;

    protected array $zeroDecimalCurrencies = [
        'BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA',
        'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF',
    ];

    public function __construct()
    {
        $this->publicKey = config('services.stripe.public_key');
        $this->secretKey = config('services.stripe.secret_key');

        if ($this->secretKey) {
            $this->client = new StripeSdk($this->secretKey);
        }
    }

    public function isEnabled(): bool
    {
        return ! empty($this->publicKey) && ! empty($this->secretKey);
    }

    public function getPublicKey(): ?string
    {
        return $this->publicKey;
    }

    /**
     * @throws RuntimeException
     */
    public function createPaymentIntent(float $amount, string $currency, array $metadata = []): array
    {
        $metadata = $this->prepareMetadata($metadata);

        $payload = [
            'amount' => $this->formatAmountForStripe($amount, $currency),
            'currency' => strtolower($currency),
            'automatic_payment_methods' => ['enabled' => true],
            'metadata' => $metadata,
        ];

        if (isset($metadata['description'])) {
            $payload['description'] = $metadata['description'];
        }

        $intent = $this->getClient()->paymentIntents->create($payload);

        return $intent->toArray();
    }

    /**
     * @throws RuntimeException
     */
    public function retrievePaymentIntent(string $intentId): array
    {
        return $this->getClient()->paymentIntents->retrieve($intentId)->toArray();
    }

    /**
     * @throws RuntimeException
     */
    protected function getClient(): StripeSdk
    {
        if (! $this->client || ! $this->isEnabled()) {
            throw new RuntimeException('Stripe integration is not configured.');
        }

        return $this->client;
    }

    protected function formatAmountForStripe(float $amount, string $currency): int
    {
        $currency = strtoupper($currency);

        if (in_array($currency, $this->zeroDecimalCurrencies, true)) {
            return (int) round($amount);
        }

        return (int) round($amount * 100);
    }

    public function normalizeAmountFromStripe(int|float $amount, string $currency): float
    {
        $currency = strtoupper($currency);

        if (in_array($currency, $this->zeroDecimalCurrencies, true)) {
            return (float) $amount;
        }

        return ((float) $amount) / 100;
    }

    public function isZeroDecimalCurrency(?string $currency): bool
    {
        if (! $currency) {
            return false;
        }

        return in_array(strtoupper($currency), $this->zeroDecimalCurrencies, true);
    }

    protected function prepareMetadata(array $metadata): array
    {
        $normalized = [];

        foreach ($metadata as $key => $value) {
            if ($value === null) {
                continue;
            }

            $normalized[$key] = (string) $value;
        }

        return $normalized;
    }
}
