<?php

namespace App\Support\Concerns;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

trait HandlesFullTextSearchFallback
{
    /**
     * Execute a search attempt and transparently fall back when a full-text index is missing.
     *
     * @param  callable():mixed  $attempt
     * @param  callable(QueryException $exception):mixed  $fallback
     * @param  string  $context
     * @return mixed
     *
     * @throws \Illuminate\Database\QueryException
     */
    protected function runFullTextAwareQuery(callable $attempt, callable $fallback, string $context)
    {
        try {
            return $attempt();
        } catch (QueryException $exception) {
            if ($this->causedByMissingFullTextIndex($exception)) {
                Log::warning('Full-text search unavailable; falling back to LIKE query.', [
                    'context' => $context,
                    'sqlstate' => $exception->getCode(),
                    'driver_code' => $exception->errorInfo[1] ?? null,
                ]);

                return $fallback($exception);
            }

            throw $exception;
        }
    }

    protected function causedByMissingFullTextIndex(QueryException $exception): bool
    {
        $driverCode = $exception->errorInfo[1] ?? null;

        if ((int) $driverCode === 1191) {
            return true;
        }

        return str_contains(strtolower($exception->getMessage()), 'fulltext index');
    }
}
