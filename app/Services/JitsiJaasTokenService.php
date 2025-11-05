<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Arr;
use RuntimeException;

class JitsiJaasTokenService
{
    protected ?string $appId;
    protected ?string $apiKey;
    protected ?string $privateKeyPath;
    protected string $provider;
    protected int $ttlMinutes;

    public function __construct(JitsiMeetingService $meetingService)
    {
        $config = config('services.jitsi.jaas', []);

        $this->appId = $config['app_id'] ?? null;
        $this->apiKey = $config['api_key'] ?? null;
        $this->privateKeyPath = $config['private_key_path'] ?? null;
        $this->ttlMinutes = (int) ($config['token_ttl_minutes'] ?? $meetingService->getTokenTtlMinutes());
        $this->provider = $meetingService->getProvider();
    }

    public function createParticipantToken(string $room, array $userContext = [], ?CarbonInterface $startsAt = null): string
    {
        return $this->createToken($room, $userContext, false, $startsAt);
    }

    public function createModeratorToken(string $room, array $userContext = [], ?CarbonInterface $startsAt = null): string
    {
        return $this->createToken($room, $userContext, true, $startsAt);
    }

    /**
     * Generate a signed JWT for Jitsi as a Service embedding.
     */
    protected function createToken(string $room, array $userContext, bool $moderator, ?CarbonInterface $startsAt = null): string
    {
        if ($this->provider !== 'jaas') {
            throw new RuntimeException('Jitsi JaaS token generation is disabled for the current provider.');
        }

        if (!$this->appId || !$this->apiKey || !$this->privateKeyPath) {
            throw new RuntimeException('Jitsi JaaS credentials are not configured. Please set JITSI_JAAS_APP_ID, JITSI_JAAS_API_KEY, and JITSI_JAAS_PRIVATE_KEY_PATH.');
        }

        $now = CarbonImmutable::now();
        $notBefore = $now->subMinute();
        $expiresAt = $startsAt
            ? CarbonImmutable::parse($startsAt)->addMinutes($this->ttlMinutes)
            : $now->addMinutes($this->ttlMinutes);

        $header = [
            'alg' => 'RS256',
            'kid' => $this->apiKey,
            'typ' => 'JWT',
        ];

        $userDetails = array_filter([
            'name' => Arr::get($userContext, 'name'),
            'email' => Arr::get($userContext, 'email'),
            'moderator' => $moderator,
        ], fn ($value) => !is_null($value) && $value !== '');

        $payload = [
            'aud' => 'jitsi',
            'iss' => 'chat',
            'sub' => $this->appId,
            'room' => $room,
            'exp' => $expiresAt->getTimestamp(),
            'nbf' => $notBefore->getTimestamp(),
            'iat' => $now->getTimestamp(),
            'context' => [
                'user' => $userDetails,
            ],
        ];

        return $this->encodeJwt($header, $payload);
    }

    protected function encodeJwt(array $header, array $payload): string
    {
        $headerEncoded = $this->base64UrlEncode(json_encode($header, JSON_THROW_ON_ERROR));
        $payloadEncoded = $this->base64UrlEncode(json_encode($payload, JSON_THROW_ON_ERROR));

        if (!file_exists($this->privateKeyPath)) {
            throw new RuntimeException("Jitsi JaaS private key file not found at: {$this->privateKeyPath}");
        }

        if (!is_readable($this->privateKeyPath)) {
            throw new RuntimeException("Jitsi JaaS private key file is not readable. Check permissions. Path: {$this->privateKeyPath}");
        }

        $privateKeyContents = file_get_contents($this->privateKeyPath);
        if ($privateKeyContents === false) {
            throw new RuntimeException("Unable to read Jitsi JaaS private key file at: {$this->privateKeyPath}");
        }

        $privateKey = openssl_pkey_get_private($privateKeyContents);
        if ($privateKey === false) {
            throw new RuntimeException('Failed to parse Jitsi JaaS private key. Ensure the file is a valid PEM key.');
        }

        $signature = '';
        $signed = openssl_sign(
            "{$headerEncoded}.{$payloadEncoded}",
            $signature,
            $privateKey,
            OPENSSL_ALGO_SHA256
        );
        openssl_free_key($privateKey);

        if ($signed === false) {
            throw new RuntimeException('Failed to sign Jitsi JaaS JWT with the provided private key.');
        }

        $signatureEncoded = $this->base64UrlEncode($signature);

        return "{$headerEncoded}.{$payloadEncoded}.{$signatureEncoded}";
    }

    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
