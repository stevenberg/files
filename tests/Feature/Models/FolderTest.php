<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Entry;
use App\Models\Folder;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FolderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake();
    }

    public function test_fillable_attributes(): void
    {
        Model::preventSilentlyDiscardingAttributes(false);

        $folder = new Folder([
            'name' => 'test',
            'path_key' => 'test',
            'url_key' => 'test',
            'implicitly_deleted' => true,
            'folder_id' => 1,
        ]);

        $this->assertSame('test', $folder->name);
        $this->assertSame('test', $folder->path_key);
        $this->assertSame('test', $folder->url_key);
        $this->assertTrue($folder->implicitly_deleted);
        $this->assertNull($folder->folder_id);
    }

    public function test_folder_relationship(): void
    {
        $parent = Folder::factory()->create();
        $child = Folder::factory()->make();

        $child->folder()->associate($parent);

        $this->assertTrue($child->folder->is($parent));
    }

    public function test_folders_relationship(): void
    {
        $parent = Folder::factory()->create();
        $child = Folder::factory()->make();

        $parent->folders()->save($child);

        $this->assertTrue($parent->folders->contains($child));
    }

    public function test_entries_relationship(): void
    {
        $folder = Folder::factory()->create();
        $entry = Entry::factory()->make();

        $folder->entries()->save($entry);

        $this->assertTrue($folder->entries->contains($entry));
    }

    public function test_root_scope(): void
    {
        $root = Folder::factory()->create();
        $child = Folder::factory()->for($root)->create();

        $folders = Folder::root()->get();

        $this->assertTrue($folders->contains($root));
        $this->assertFalse($folders->contains($child));
    }

    public function test_implicitly_deleted_scope(): void
    {
        $folders = Folder::factory()
            ->count(3)
            ->state(new Sequence(
                ['implicitly_deleted' => false],
                ['implicitly_deleted' => false],
                ['implicitly_deleted' => true],
            ))
            ->create()
        ;

        $folders->skip(1)->each->delete();

        $implicitlyDeleted = Folder::implicitlyDeleted()->get();

        $this->assertFalse($implicitlyDeleted->contains($folders[0]));
        $this->assertFalse($implicitlyDeleted->contains($folders[1]));
        $this->assertTrue($implicitlyDeleted->contains($folders[2]));
    }

    public function test_ancestors(): void
    {
        $root = Folder::factory()->create();
        $parent = Folder::factory()->for($root)->create();
        $child = Folder::factory()->for($parent)->create();

        $expected = [
            $root,
            $parent,
        ];

        $ancestors = $child->ancestors;

        $ancestors->zip($expected)->eachSpread(function ($folder, $expected) {
            $this->assertTrue($folder->is($expected));
        });
    }

    public function test_uploads_path(): void
    {
        $parent = Folder::factory()->create([
            'path_key' => 'parent',
        ]);
        $child = Folder::factory()->for($parent)->create([
            'path_key' => 'child',
        ]);

        $this->assertSame('uploads/parent/child', $child->uploadsPath);
    }

    public function test_files_path(): void
    {
        $parent = Folder::factory()->create([
            'path_key' => 'parent',
        ]);
        $child = Folder::factory()->for($parent)->create([
            'path_key' => 'child',
        ]);

        $this->assertSame('files/parent/child', $child->filesPath);
    }

    public function test_implicitly_delete(): void
    {
        $folder = Folder::factory()->create();

        $folder->implicitlyDelete();

        $this->assertSoftDeleted($folder);
        $this->assertTrue($folder->implicitly_deleted);
    }

    public function test_ordered_by_name(): void
    {
        $folders = Folder::factory()
            ->count(3)
            ->state(new Sequence(
                ['name' => 'B'],
                ['name' => 'A'],
                ['name' => 'C'],
            ))
            ->create()
        ;

        $expected = [
            $folders[1],
            $folders[0],
            $folders[2],
        ];

        $folders = Folder::all();

        $folders->zip($expected)->eachSpread(function ($folder, $expected) {
            $this->assertTrue($folder->is($expected));
        });
    }

    public function test_sets_path_key_on_creating(): void
    {
        $folder = Folder::factory()->create([
            'name' => 'Test Folder',
        ]);

        $this->assertSame('test-folder', $folder->path_key);
    }

    public function test_sets_url_key_on_creating(): void
    {
        $parent = Folder::factory()->create([
            'path_key' => 'parent',
        ]);
        $folder = Folder::factory()->for($parent)->create([
            'name' => 'Test Folder',
        ]);

        $this->assertSame('parent-test-folder', $folder->url_key);
    }

    public function test_makes_directories_on_created(): void
    {
        $folder = Folder::factory()->create([
            'name' => 'Test Folder',
        ]);

        Storage::assertExists('uploads/test-folder');
        Storage::assertExists('files/test-folder');
    }

    public function test_soft_deletion(): void
    {
        $parent = Folder::factory()->create([
            'name' => 'Test Folder',
        ]);
        $child = Folder::factory()->for($parent)->create();
        $deletedChild = Folder::factory()->for($parent)->create([
            'implicitly_deleted' => false,
        ]);
        $deletedChild->delete();
        $entry = Entry::factory()->for($parent)->create();
        $deletedEntry = Entry::factory()->for($parent)->create([
            'implicitly_deleted' => false,
        ]);
        $deletedEntry->delete();

        $parent->delete();

        $this->assertSoftDeleted($parent);
        $this->assertSoftDeleted($child);
        $this->assertSoftDeleted($deletedChild);
        $this->assertSoftDeleted($entry);
        $this->assertSoftDeleted($deletedEntry);
        Storage::assertMissing('uploads/test-folder');
        Storage::assertExists('files/test-folder');

        $parent->restore();

        $this->assertNotSoftDeleted($parent);
        $this->assertNotSoftDeleted($child);
        $this->assertSoftDeleted($deletedChild);
        $this->assertNotSoftDeleted($entry);
        $this->assertSoftDeleted($deletedEntry);
        Storage::assertExists('uploads/test-folder');
        Storage::assertExists('files/test-folder');
    }

    public function test_force_deletion(): void
    {
        $parent = Folder::factory()->create([
            'name' => 'Test Folder',
        ]);
        $child = Folder::factory()->for($parent)->create();
        $entry = Entry::factory()->for($parent)->create();

        $parent->forceDelete();

        $this->assertModelMissing($child);
        $this->assertModelMissing($entry);
        Storage::assertMissing('uploads/test-folder');
        Storage::assertMissing('files/test-folder');
    }
}
