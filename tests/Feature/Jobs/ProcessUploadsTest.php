<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs;

use App\Jobs\ProcessUpload;
use App\Jobs\ProcessUploads;
use App\Models\Folder;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProcessUploadsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        Storage::fake();
        Storage::fake('public');
    }

    public function test_job(): void
    {
        $folder = Folder::factory()->create([
            'name' => 'Test',
        ]);

        Storage::put('uploads/test/test.txt', 'test');

        $job = new ProcessUploads;
        $job->handle();

        Queue::assertPushed(function (ProcessUpload $job) use ($folder) {
            return $job->folder->is($folder) && $job->path === 'uploads/test/test.txt';
        });
    }
}
