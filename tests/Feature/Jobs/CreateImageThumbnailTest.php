<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs;

use App\Jobs\CreateImageThumbnail;
use App\Models\Entry;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreateImageThumbnailTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake();
        Storage::fake('public');
    }

    public function test_jpg(): void
    {
        copy(base_path('tests/fixtures/test.jpg'), Storage::path('test.jpg'));

        $entry = Entry::factory()->forFolder()->create([
            'path' => 'test.jpg',
        ]);

        $job = new CreateImageThumbnail($entry);
        $job->handle();

        $this->assertSame(1, $entry->thumbnail->width);
        $this->assertSame(1, $entry->thumbnail->height);

        $entry->thumbnail->paths()->each(function ($path) {
            Storage::assertExists($path);
        });
    }

    public function test_png(): void
    {
        copy(base_path('tests/fixtures/test.png'), Storage::path('test.png'));

        $entry = Entry::factory()->forFolder()->create([
            'path' => 'test.png',
        ]);

        $job = new CreateImageThumbnail($entry);
        $job->handle();

        $this->assertSame(1, $entry->thumbnail->width);
        $this->assertSame(1, $entry->thumbnail->height);

        $entry->thumbnail->paths()->each(function ($path) {
            Storage::assertExists($path);
        });
    }
}
