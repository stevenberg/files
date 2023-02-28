<?php

declare(strict_types=1);

namespace App\Values\Thumbnails;

enum Size: string
{
    case S250 = '250';
    case S500 = '500';
    case S750 = '750';
    case S1000 = '1000';
    case S1250 = '1250';
    case S1500 = '1500';

    public function intValue(): int
    {
        return intval($this->value);
    }
}
