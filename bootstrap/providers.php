<?php

$providers = [
    App\Providers\AppServiceProvider::class,
];

$appEnv = env('APP_ENV', 'production');

if (
    $appEnv !== 'production'
    && class_exists(\Laravel\Telescope\TelescopeApplicationServiceProvider::class)
) {
    $providers[] = App\Providers\TelescopeServiceProvider::class;
}

return $providers;
