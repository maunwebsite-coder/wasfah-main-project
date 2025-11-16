<?php

namespace App\Support;

class GoogleMeetAccountChooser
{
    private const BASE_URL = 'https://accounts.google.com/AccountChooser';
    private const DEFAULT_DESTINATION = 'https://meet.google.com/';

    /**
     * Build a Google Account Chooser URL that preselects the given email and Meet link.
     */
    public static function build(?string $email, ?string $meetLink, ?string $locale = null): string
    {
        $params = [
            'continue' => static::sanitizeDestination($meetLink),
        ];

        if ($normalizedEmail = static::normalizeEmail($email)) {
            $params['Email'] = $normalizedEmail;
        }

        if ($normalizedLocale = static::normalizeLocale($locale)) {
            $params['hl'] = $normalizedLocale;
        }

        $query = http_build_query($params, '', '&', PHP_QUERY_RFC3986);

        return static::BASE_URL . '?' . $query;
    }

    protected static function normalizeEmail(?string $email): ?string
    {
        if (!is_string($email)) {
            return null;
        }

        $normalized = strtolower(trim($email));

        return filter_var($normalized, FILTER_VALIDATE_EMAIL) ? $normalized : null;
    }

    protected static function sanitizeDestination(?string $url): string
    {
        if (is_string($url)) {
            $normalized = trim($url);

            if ($normalized !== '' && filter_var($normalized, FILTER_VALIDATE_URL)) {
                return $normalized;
            }
        }

        return static::DEFAULT_DESTINATION;
    }

    protected static function normalizeLocale(?string $locale): ?string
    {
        if (!is_string($locale)) {
            return null;
        }

        $value = trim($locale);

        if ($value === '') {
            return null;
        }

        $value = strtolower(str_replace('_', '-', $value));
        $parts = explode('-', $value);
        $primary = $parts[0] ?? '';

        return preg_match('/^[a-z]{2}$/', $primary) ? $primary : null;
    }
}
