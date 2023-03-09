<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Support\Str;
use Illuminate\View\Component;

class Sprite extends Component
{
    public function render(): string
    {
        if (is_file(public_path('/hot'))) {
            $path = base_path('node_modules/@fortawesome/fontawesome-pro/sprites/duotone.svg');
            $svg = file_get_contents($path);

            if ($svg === false) {
                throw new \Exception('Failed to read icon sprite file');
            }

            // Remove the XML declaration.
            return Str::remove('<?xml version="1.0" encoding="UTF-8"?>', $svg);
        }

        return '';
    }
}
