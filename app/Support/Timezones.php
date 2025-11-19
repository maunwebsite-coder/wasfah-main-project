<?php

namespace App\Support;

use DateTimeImmutable;
use DateTimeZone;

class Timezones
{
    /**
     * Cached list of timezone options.
     *
     * @var array<string, string>|null
     */
    protected static ?array $options = null;

    /**
     * Return an associative array of timezone => formatted label.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        if (static::$options !== null) {
            return static::$options;
        }

        $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        $zones = [];

        foreach (DateTimeZone::listIdentifiers(DateTimeZone::ALL) as $name) {
            try {
                $timezone = new DateTimeZone($name);
            } catch (\Throwable $exception) {
                continue;
            }

            $offsetSeconds = $timezone->getOffset($now);
            $offset = static::formatOffset($offsetSeconds);
            $prettyName = str_replace('_', ' ', $name);

            $zones[] = [
                'name' => $name,
                'label' => sprintf('(GMT%s) %s', $offset, $prettyName),
                'offset' => $offsetSeconds,
            ];
        }

        usort($zones, static function (array $a, array $b): int {
            if ($a['offset'] === $b['offset']) {
                return strcmp($a['name'], $b['name']);
            }

            return $a['offset'] <=> $b['offset'];
        });

        static::$options = [];

        foreach ($zones as $zone) {
            static::$options[$zone['name']] = $zone['label'];
        }

        return static::$options;
    }

    /**
     * Check if the provided timezone is valid.
     */
    public static function isValid(?string $timezone): bool
    {
        if (! is_string($timezone) || $timezone === '') {
            return false;
        }

        try {
            new DateTimeZone($timezone);

            return true;
        } catch (\Throwable $exception) {
            return false;
        }
    }

    protected static function formatOffset(int $seconds): string
    {
        $sign = $seconds >= 0 ? '+' : '-';
        $abs = abs($seconds);
        $hours = str_pad((string) floor($abs / 3600), 2, '0', STR_PAD_LEFT);
        $minutes = str_pad((string) floor(($abs % 3600) / 60), 2, '0', STR_PAD_LEFT);

        return sprintf('%s%s:%s', $sign, $hours, $minutes);
    }
}
