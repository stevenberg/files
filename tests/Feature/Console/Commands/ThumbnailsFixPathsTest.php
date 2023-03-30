<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands;

use App\Models\Entry;
use App\Models\Folder;
use App\Models\Thumbnail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ThumbnailsFixPathsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake();
        Artisan::call('db:setup');
    }

    public function test_command(): void
    {
        $folder = Folder::factory()->inRoot()->create([
            'name' => 'Test Folder',
        ]);
        $entry = Entry::factory()->for($folder)->create([
            'name' => 'Test Entry',
            'path' => 'files/test-folder/test-entry/asdf.png',
        ]);
        $thumbnail = Thumbnail::factory()
            ->for($entry)
            ->create([
                'path_template' => 'thumbnails/test-folder/asdf_[SHAPE]_[SIZE].png',
            ])
        ;

        $oldPaths = $thumbnail->paths();

        $this->artisan('thumbnails:fix_paths')->assertSuccessful();

        $thumbnail->refresh();

        $this->assertSame(
            'thumbnails/test-folder/test-entry/asdf_[SHAPE]_[SIZE].png',
            $thumbnail->path_template,
        );

        $oldPaths->each(function ($path) {
            Storage::assertMissing($path);
        });

        $thumbnail->paths()->each(function ($path) {
            Storage::assertExists($path);
        });
    }
}
