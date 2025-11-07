<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;

Broadcast::channel('notifications.{userId}', function ($user, int $userId) {
    $authenticatedUser = $user ?? Auth::user();

    if (! $authenticatedUser) {
        return false;
    }

    return (int) $authenticatedUser->id === (int) $userId;
});

Broadcast::channel('App.Models.User.{id}', function ($user, int $id) {
    $authenticatedUser = $user ?? Auth::user();

    if (! $authenticatedUser) {
        return false;
    }

    return (int) $authenticatedUser->id === (int) $id;
});
