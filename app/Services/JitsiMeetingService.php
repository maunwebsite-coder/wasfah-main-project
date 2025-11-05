<?php

namespace App\Services;

use Carbon\CarbonInterface;
use Illuminate\Support\Str;

class JitsiMeetingService
{
    protected string $baseUrl;
    protected string $roomPrefix;
    protected int $defaultDuration;
    protected string $provider;
    protected ?string $jaasAppId = null;
    protected int $jaasTokenTtl;

    public function __construct(?string $baseUrl = null, ?string $roomPrefix = null, ?int $defaultDuration = null)
    {
        $config = config('services.jitsi');

        $this->provider = $config['provider'] ?? 'meet';
        $this->roomPrefix = Str::slug($roomPrefix ?? $config['room_prefix'] ?? 'wasfah');
        $this->defaultDuration = $defaultDuration ?? (int) ($config['default_duration'] ?? 90);
        $this->jaasTokenTtl = (int) ($config['jaas']['token_ttl_minutes'] ?? 240);

        if ($this->provider === 'jaas') {
            $jaasConfig = $config['jaas'] ?? [];
            $this->jaasAppId = $jaasConfig['app_id'] ?? null;
            $this->baseUrl = rtrim($jaasConfig['base_url'] ?? 'https://8x8.vc', '/');
        } else {
            $this->baseUrl = rtrim($baseUrl ?? $config['base_url'] ?? 'https://meet.jit.si', '/');
        }
    }

    /**
     * إنشاء اجتماع جديد على Jitsi مع اسم غرفة ورابط فريدين.
     */
    public function createMeeting(string $title, int $userId, ?CarbonInterface $startsAt = null): array
    {
        $room = $this->generateRoomName($title, $userId, $startsAt);
        $roomPath = $room;
        $passcode = $this->provider === 'jaas' ? null : $this->generatePasscode();
        $url = "{$this->baseUrl}/{$roomPath}";

        if ($this->provider === 'jaas' && $this->jaasAppId) {
            $roomPath = "{$this->jaasAppId}/{$room}";
            $url = "{$this->baseUrl}/{$roomPath}";
        }

        return [
            'room' => $room,
            'room_path' => $roomPath,
            'url' => $url,
            'passcode' => $passcode,
            'provider' => $this->provider,
            'starts_at' => $startsAt,
            'ends_at' => $startsAt ? $startsAt->copy()->addMinutes($this->defaultDuration) : null,
        ];
    }

    /**
     * إنشاء اسم غرفة فريد بالاعتماد على اسم الورشة والشيف.
     */
    public function generateRoomName(string $title, int $userId, ?CarbonInterface $startsAt = null): string
    {
        $timestamp = ($startsAt ?? now())->format('YmdHis');
        $normalizedTitle = Str::slug($title, '-');
        $randomSuffix = Str::upper(Str::random(5));

        return "{$this->roomPrefix}-{$userId}-{$timestamp}-{$normalizedTitle}-{$randomSuffix}";
    }

    /**
     * توليد كلمة مرور رقمية بسيطة لاستخدامها في الغرفة (اختياري).
     */
    public function generatePasscode(int $length = 6): string
    {
        $length = max(4, min($length, 10));
        $digits = '';

        for ($i = 0; $i < $length; $i++) {
            $digits .= random_int(0, 9);
        }

        return $digits;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getJaasAppId(): ?string
    {
        return $this->jaasAppId;
    }

    public function getTokenTtlMinutes(): int
    {
        return $this->jaasTokenTtl;
    }
}
