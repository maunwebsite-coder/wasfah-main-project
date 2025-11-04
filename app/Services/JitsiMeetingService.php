<?php

namespace App\Services;

use Carbon\CarbonInterface;
use Illuminate\Support\Str;

class JitsiMeetingService
{
    protected string $baseUrl;
    protected string $roomPrefix;
    protected int $defaultDuration;

    public function __construct(?string $baseUrl = null, ?string $roomPrefix = null, ?int $defaultDuration = null)
    {
        $this->baseUrl = rtrim($baseUrl ?? config('services.jitsi.base_url', 'https://meet.jit.si'), '/');
        $this->roomPrefix = Str::slug($roomPrefix ?? config('services.jitsi.room_prefix', 'wasfah'));
        $this->defaultDuration = $defaultDuration ?? (int) config('services.jitsi.default_duration', 90);
    }

    /**
     * إنشاء اجتماع جديد على Jitsi مع اسم غرفة ورابط فريدين.
     */
    public function createMeeting(string $title, int $userId, ?CarbonInterface $startsAt = null): array
    {
        $room = $this->generateRoomName($title, $userId, $startsAt);
        $passcode = $this->generatePasscode();

        return [
            'room' => $room,
            'url' => "{$this->baseUrl}/{$room}",
            'passcode' => $passcode,
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
}
