<?php

namespace App\Support;

use Illuminate\Support\Arr;

class ViteHot
{
    /**
     * Decide whether the Vite dev server should be used.
     */
    public static function shouldUseHotReload(): bool
    {
        if (! app()->environment(['local', 'development', 'testing'])) {
            return false;
        }

        $hotFile = public_path('hot');

        if (! is_file($hotFile)) {
            return false;
        }

        $hotTarget = trim((string) @file_get_contents($hotFile));

        if ($hotTarget === '') {
            return false;
        }

        $info = parse_url($hotTarget);

        if ($info === false || empty($info['host'])) {
            return false;
        }

        $host = static::normalizeHost($info['host']);
        $port = (int) Arr::get($info, 'port', 5173);
        $timeout = (float) config('app.vite_hot_ping_timeout', 0.15);
        $protocol = (Arr::get($info, 'scheme') === 'https') ? 'ssl://' : '';

        try {
            $connection = @fsockopen($protocol . $host, $port, $errno, $errstr, $timeout);

            if (is_resource($connection)) {
                fclose($connection);

                return true;
            }
        } catch (\Throwable) {
            // Intentionally ignored – we just fall back to built assets.
        }

        return false;
    }

    protected static function normalizeHost(string $host): string
    {
        $host = trim($host, '[]');

        return $host === '::1' ? '127.0.0.1' : $host;
    }
}
