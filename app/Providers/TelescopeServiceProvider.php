<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->shouldDisableTelescope()) {
            config(['telescope.enabled' => false]);

            return;
        }

        $this->hideSensitiveRequestDetails();

        $isLocal = $this->app->environment('local');

        Telescope::filter(function (IncomingEntry $entry) use ($isLocal) {
            return $isLocal ||
                   $entry->isReportableException() ||
                   $entry->isFailedRequest() ||
                   $entry->isFailedJob() ||
                   $entry->isScheduledTask() ||
                   $entry->hasMonitoredTag();
        });
    }

    /**
     * Determine if Telescope should be disabled for this request.
     */
    protected function shouldDisableTelescope(): bool
    {
        if (! config('telescope.enabled', false)) {
            return true;
        }

        return ! $this->telescopeEntriesTableExists();
    }

    /**
     * Ensure the Telescope entries table exists before attempting to record data.
     */
    protected function telescopeEntriesTableExists(): bool
    {
        static $hasTable;

        if ($hasTable !== null) {
            return $hasTable;
        }

        $connection = config('telescope.storage.database.connection', config('database.default'));

        try {
            $hasTable = Schema::connection($connection)->hasTable('telescope_entries');
        } catch (\Throwable $exception) {
            $hasTable = false;
        }

        return $hasTable;
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewTelescope', function ($user) {
            return in_array($user->email, [
                //
            ]);
        });
    }
}
