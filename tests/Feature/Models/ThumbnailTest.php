<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Entry;
use App\Models\Thumbnail;
use App\Values\Thumbnails\Shape;
use App\Values\Thumbnails\Size;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ThumbnailTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake();
        Storage::fake('public');
    }

    public function test_fillable_attributes(): void
    {
        Model::preventSilentlyDiscardingAttributes(false);

        $thumbnail = new Thumbnail([
            'path_template' => 'test',
            'width' => 1,
            'height' => 1,
            'entry_id' => 1,
        ]);

        $this->assertSame('test', $thumbnail->path_template);
        $this->assertSame(1, $thumbnail->width);
        $this->assertSame(1, $thumbnail->height);
        $this->assertNull($thumbnail->entry_id);
    }

    public function test_entry_relationship(): void
    {
        $entry = Entry::factory()->forFolder()->create();
        $thumbnail = Thumbnail::factory()->make();

        $thumbnail->entry()->associate($entry);

        $this->assertTrue($thumbnail->entry->is($entry));
    }

    public function test_paths(): void
    {
        $thumbnail = Thumbnail::factory()
            ->for(Entry::factory()->forFolder())
            ->create([
                'path_template' => 'test_[SHAPE]_[SIZE].jpg',
            ])
        ;

        $expected = collect([
            'test_original_250.jpg',
            'test_original_500.jpg',
            'test_original_750.jpg',
            'test_original_1000.jpg',
            'test_original_1250.jpg',
            'test_original_1500.jpg',
            'test_square_250.jpg',
            'test_square_500.jpg',
            'test_square_750.jpg',
            'test_square_1000.jpg',
            'test_square_1250.jpg',
            'test_square_1500.jpg',
        ]);

        $this->assertEquals($expected, $thumbnail->paths());
    }

    public function test_paths_with_argument(): void
    {
        $thumbnail = Thumbnail::factory()
            ->for(Entry::factory()->forFolder())
            ->create([
                'path_template' => 'test_[SHAPE]_[SIZE].jpg',
            ])
        ;

        $expected = collect([
            'test_square_250.jpg',
            'test_square_500.jpg',
            'test_square_750.jpg',
            'test_square_1000.jpg',
            'test_square_1250.jpg',
            'test_square_1500.jpg',
        ]);

        $this->assertEquals($expected, $thumbnail->paths(Shape::Square));
    }

    public function test_path(): void
    {
        $thumbnail = Thumbnail::factory()
            ->for(Entry::factory()->forFolder())
            ->create([
                'path_template' => 'test_[SHAPE]_[SIZE].jpg',
            ])
        ;

        $this->assertSame('test_original_250.jpg', $thumbnail->path(Shape::Original, Size::S250));
    }

    public function test_make_path(): void
    {
        $this->assertSame(
            'test_original_250.jpg',
            Thumbnail::makePath('test_[SHAPE]_[SIZE].jpg', Shape::Original, Size::S250),
        );
    }

    public function test_deletes_path_on_force_deleted(): void
    {
        $thumbnail = Thumbnail::factory()
            ->for(Entry::factory()->forFolder())
            ->create()
        ;

        $thumbnail->paths()->each(function ($path) {
            Storage::assertExists($path);
        });

        $thumbnail->forceDelete();

        $thumbnail->paths()->each(function ($path) {
            Storage::assertMissing($path);
        });
    }
}
