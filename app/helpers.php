<?php

declare(strict_types=1);

namespace App;

function status_message(): ?string
{
    if (! session()->has('status')) {
        return null;
    }

    return match (session('status')) {
        'profile-information-updated' => 'Profile updated.',
        'password-updated' => 'Password updated.',
        'two-factor-authentication-confirmed' => 'Two factor authentication enabled.',
        'two-factor-authentication-disabled' => 'Two factor authentication disabled.',
        'two-factor-authentication-enabled' => null,
        default => session('status'),
    };
}
