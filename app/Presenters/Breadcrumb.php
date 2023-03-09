<?php

declare(strict_types=1);

namespace App\Presenters;

class Breadcrumb
{
    public function __construct(public string $name, public string $url)
    {
    }
}
