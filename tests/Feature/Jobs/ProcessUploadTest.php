<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs;

use App\Jobs\ProcessUpload;
use App\Models\Folder;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProcessUploadTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake();
    }

    public function test_job(): void
    {
        $folder = Folder::factory()->create([
            'name' => 'Test',
        ]);

        Storage::put('uploads/test/test.txt', 'test');

        $job = new ProcessUpload($folder, 'uploads/test/test.txt');
        $job->handle();

        $entry = $folder->entries->first();

        $this->assertSame('test', $entry->name);
        Storage::assertExists($entry->path);
        $this->assertSame('test', Storage::get($entry->path));
        Storage::assertMissing('uploads/test/test.txt');
    }
}
