<?php

declare(strict_types=1);

namespace Tests\Unit\Values\Thumbnails;

use App\Values\Thumbnails\Shape;
use PHPUnit\Framework\TestCase;

class ShapeTest extends TestCase
{
    public function test_values(): void
    {
        $cases = [
            [Shape::Original, 'original'],
            [Shape::Square, 'square'],
        ];

        foreach ($cases as [$case, $value]) {
            $this->assertSame($value, $case->value);
        }
    }
}
