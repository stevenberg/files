<?php

declare(strict_types=1);

namespace Tests\Unit\Values\Thumbnails;

use App\Values\Thumbnails\Size;
use PHPUnit\Framework\TestCase;

class SizeTest extends TestCase
{
    public function test_values(): void
    {
        $cases = [
            [Size::S250, '250'],
            [Size::S500, '500'],
            [Size::S750, '750'],
            [Size::S1000, '1000'],
            [Size::S1250, '1250'],
            [Size::S1500, '1500'],
        ];

        foreach ($cases as [$case, $value]) {
            $this->assertSame($value, $case->value);
        }
    }

    public function test_int_values(): void
    {
        $cases = [
            [Size::S250, 250],
            [Size::S500, 500],
            [Size::S750, 750],
            [Size::S1000, 1000],
            [Size::S1250, 1250],
            [Size::S1500, 1500],
        ];

        foreach ($cases as [$case, $value]) {
            $this->assertSame($value, $case->intValue());
        }
    }
}
