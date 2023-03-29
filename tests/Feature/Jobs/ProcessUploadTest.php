<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs;

use App\Jobs\CreateThumbnail;
use App\Jobs\ProcessUpload;
use App\Models\Folder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProcessUploadTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        Storage::fake();
        Storage::fake('public');
        Artisan::call('db:setup');
    }

    public function test_job(): void
    {
        $folder = Folder::factory()->inRoot()->create([
            'name' => 'Test',
        ]);

        Storage::put('uploads/test/test.txt', 'test');

        $job = new ProcessUpload($folder, 'uploads/test/test.txt', 'Test File');
        $job->handle();

        $entry = $folder->entries->first();

        $this->assertSame('Test File', $entry->name);
        Storage::assertExists($entry->path);
        $this->assertSame('test', Storage::get($entry->path));
        Storage::assertMissing('uploads/test/test.txt');

        Queue::assertPushed(function (CreateThumbnail $job) use ($entry) {
            return $job->entry->is($entry);
        });
    }
}
