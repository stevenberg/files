<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs;

use App\Jobs\CreateImageThumbnail;
use App\Models\Entry;
use App\Models\Folder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreateImageThumbnailTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake();
        Artisan::call('db:setup');
    }

    public function test_jpg(): void
    {
        $folder = Folder::factory()->inRoot()->create([
            'name' => 'Test',
        ]);
        $entry = Entry::factory()->for($folder)->create([
            'name' => 'Test',
            'path' => 'files/test/test.jpg',
        ]);

        copy(base_path('tests/fixtures/test.jpg'), Storage::path('files/test/test.jpg'));

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
        $folder = Folder::factory()->inRoot()->create([
            'name' => 'Test',
        ]);
        $entry = Entry::factory()->for($folder)->create([
            'name' => 'Test',
            'path' => 'files/test/test.png',
        ]);

        copy(base_path('tests/fixtures/test.png'), Storage::path('files/test/test.png'));

        $job = new CreateImageThumbnail($entry);
        $job->handle();

        $this->assertSame(1, $entry->thumbnail->width);
        $this->assertSame(1, $entry->thumbnail->height);

        $entry->thumbnail->paths()->each(function ($path) {
            Storage::assertExists($path);
        });
    }
}
