<?php

declare(strict_types=1);

namespace App;

function status_message(string $key = 'status'): ?string
{
    if (! session()->has($key)) {
        return null;
    }

    return match (session($key)) {
        'profile-information-updated' => 'Profile updated.',
        'password-updated' => 'Password updated.',
        'two-factor-authentication-confirmed' => 'Two factor authentication enabled.',
        'two-factor-authentication-disabled' => 'Two factor authentication disabled.',
        'two-factor-authentication-enabled' => null,
        default => session($key),
    };
}
