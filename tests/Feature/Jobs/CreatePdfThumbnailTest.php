<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs;

use App\Jobs\CreatePdfThumbnail;
use App\Models\Entry;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreatePdfThumbnailTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake();
        Storage::fake('public');
    }

    public function test_job(): void
    {
        copy(base_path('tests/fixtures/test.pdf'), Storage::path('test.pdf'));

        $entry = Entry::factory()->forFolder()->create([
            'path' => 'test.pdf',
        ]);

        $job = new CreatePdfThumbnail($entry);
        $job->handle();

        $this->assertSame(1500, $entry->thumbnail->width);
        $this->assertSame(1941, $entry->thumbnail->height);

        $entry->thumbnail->paths()->each(function ($path) {
            Storage::assertExists($path);
        });
    }
}
