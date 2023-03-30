<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs;

use App\Jobs\CreateImageThumbnail;
use App\Jobs\CreatePdfThumbnail;
use App\Jobs\CreateThumbnail;
use App\Models\Entry;
use App\Models\Folder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreateThumbnailTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        Storage::fake();
        Artisan::call('db:setup');
    }

    public function test_pdf(): void
    {
        copy(base_path('tests/fixtures/test.pdf'), Storage::path('test.pdf'));

        $pdfEntry = Entry::factory()->for(Folder::factory()->inRoot())->create([
            'path' => 'test.pdf',
        ]);

        $job = new CreateThumbnail($pdfEntry);
        $job->handle();

        Queue::assertPushed(function (CreatePdfThumbnail $job) use ($pdfEntry) {
            return $job->entry->is($pdfEntry);
        });
    }

    public function test_jpg(): void
    {
        copy(base_path('tests/fixtures/test.jpg'), Storage::path('test.jpg'));

        $entry = Entry::factory()->for(Folder::factory()->inRoot())->create([
            'path' => 'test.jpg',
        ]);

        $job = new CreateThumbnail($entry);
        $job->handle();

        Queue::assertPushed(function (CreateImageThumbnail $job) use ($entry) {
            return $job->entry->is($entry);
        });
    }

    public function test_png(): void
    {
        copy(base_path('tests/fixtures/test.png'), Storage::path('test.png'));

        $entry = Entry::factory()->for(Folder::factory()->inRoot())->create([
            'path' => 'test.png',
        ]);

        $job = new CreateThumbnail($entry);
        $job->handle();

        Queue::assertPushed(function (CreateImageThumbnail $job) use ($entry) {
            return $job->entry->is($entry);
        });
    }

    public function test_txt(): void
    {
        copy(base_path('tests/fixtures/test.txt'), Storage::path('test.txt'));

        $entry = Entry::factory()->for(Folder::factory()->inRoot())->create([
            'path' => 'test.txt',
        ]);

        $job = new CreateThumbnail($entry);
        $job->handle();

        Queue::assertNothingPushed();
    }
}
