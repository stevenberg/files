<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Entry;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FolderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake();
        Storage::fake('public');
        Artisan::call('db:setup');
    }

    public function test_fillable_attributes(): void
    {
        Model::preventSilentlyDiscardingAttributes(false);

        $folder = new Folder([
            'restricted' => true,
            'name' => 'test',
            'path_key' => 'test',
            'url_key' => 'test',
            'implicitly_deleted' => true,
            'folder_id' => 1,
        ]);

        $this->assertSame(true, $folder->restricted);
        $this->assertSame('test', $folder->name);
        $this->assertSame('test', $folder->path_key);
        $this->assertSame('test', $folder->url_key);
        $this->assertTrue($folder->implicitly_deleted);
        $this->assertNull($folder->folder_id);
    }

    public function test_folder_relationship(): void
    {
        $parent = Folder::factory()->inRoot()->create();
        $child = Folder::factory()->make();

        $child->folder()->associate($parent);

        $this->assertTrue($child->folder->is($parent));
    }

    public function test_folders_relationship(): void
    {
        $parent = Folder::factory()->inRoot()->create();
        $child = Folder::factory()->make();

        $parent->folders()->save($child);

        $this->assertTrue($parent->folders->contains($child));
    }

    public function test_entries_relationship(): void
    {
        $folder = Folder::factory()->inRoot()->create();
        $entry = Entry::factory()->make();

        $folder->entries()->save($entry);

        $this->assertTrue($folder->entries->contains($entry));
    }

    public function test_users_relationship(): void
    {
        $folder = Folder::factory()->inRoot()->create();
        $user = User::factory()->create();

        $folder->users()->attach($user);

        $this->assertTrue($folder->users->contains($user));
    }

    public function test_root_scope(): void
    {
        $root = Folder::root()->first();
        $child = Folder::factory()->inRoot()->create();

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
            ->inRoot()
            ->create()
        ;

        $folders->skip(1)->each->delete();

        $implicitlyDeleted = Folder::implicitlyDeleted()->get();

        $this->assertFalse($implicitlyDeleted->contains($folders[0]));
        $this->assertFalse($implicitlyDeleted->contains($folders[1]));
        $this->assertTrue($implicitlyDeleted->contains($folders[2]));
    }

    public function test_explicitly_deleted_scope(): void
    {
        $folders = Folder::factory()
            ->count(3)
            ->state(new Sequence(
                ['implicitly_deleted' => false],
                ['implicitly_deleted' => false],
                ['implicitly_deleted' => true],
            ))
            ->inRoot()
            ->create()
        ;

        $folders->skip(1)->each->delete();

        $explicitlyDeleted = Folder::explicitlyDeleted()->get();

        $this->assertFalse($explicitlyDeleted->contains($folders[0]));
        $this->assertTrue($explicitlyDeleted->contains($folders[1]));
        $this->assertFalse($explicitlyDeleted->contains($folders[2]));
    }

    public function test_ancestors(): void
    {
        $parent = Folder::factory()->inRoot()->create();
        $child = Folder::factory()->for($parent)->create();

        $expected = [
            $parent,
        ];

        $ancestors = $child->ancestors;

        $ancestors->zip($expected)->eachSpread(function ($folder, $expected) {
            $this->assertTrue($folder->is($expected));
        });
    }

    public function test_is_root(): void
    {
        $root = Folder::root()->first();

        $this->assertTrue($root->isRoot);

        $folder = Folder::factory()->inRoot()->create();

        $this->assertFalse($folder->isRoot);
    }

    public function test_is_restricted(): void
    {
        $parent = Folder::factory()->inRoot();
        $folder = Folder::factory()->for($parent)->create();

        $this->assertFalse($folder->isRestricted);

        $folder->restricted = true;

        $this->assertTrue($folder->isRestricted);

        $folder->restricted = false;
        $folder->folder->restricted = true;

        $this->assertTrue($folder->isRestricted);
    }

    public function test_uploads_path(): void
    {
        $parent = Folder::factory()->inRoot()->create([
            'path_key' => 'parent',
        ]);
        $child = Folder::factory()->for($parent)->create([
            'path_key' => 'child',
        ]);

        $this->assertSame('uploads/parent/child', $child->uploadsPath);
    }

    public function test_files_path(): void
    {
        $parent = Folder::factory()->inRoot()->create([
            'path_key' => 'parent',
        ]);
        $child = Folder::factory()->for($parent)->create([
            'path_key' => 'child',
        ]);

        $this->assertSame('files/parent/child', $child->filesPath);
    }

    public function test_thumbnails_path(): void
    {
        $parent = Folder::factory()->inRoot()->create([
            'path_key' => 'parent',
        ]);
        $child = Folder::factory()->for($parent)->create([
            'path_key' => 'child',
        ]);

        $this->assertSame('thumbnails/parent/child', $child->thumbnailsPath);
    }

    public function test_implicitly_delete(): void
    {
        $folder = Folder::factory()->inRoot()->create();

        $folder->implicitlyDelete();

        $this->assertSoftDeleted($folder);
        $this->assertTrue($folder->implicitly_deleted);
        Storage::assertExists($folder->uploadsPath);
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
            ->inRoot()
            ->create()
        ;

        $expected = [
            $folders[1],
            $folders[0],
            $folders[2],
        ];

        $folders = Folder::root()->first()->folders;

        $folders->zip($expected)->eachSpread(function ($folder, $expected) {
            $this->assertTrue($folder->is($expected));
        });
    }

    public function test_sets_path_key_on_creating(): void
    {
        $folder = Folder::factory()->inRoot()->create([
            'name' => 'Test Folder',
        ]);

        $this->assertSame('test-folder', $folder->path_key);
    }

    public function test_sets_url_key_on_creating(): void
    {
        $parent = Folder::factory()->inRoot()->create([
            'path_key' => 'parent',
        ]);
        $folder = Folder::factory()->for($parent)->create([
            'name' => 'Test Folder',
        ]);

        $this->assertSame('parent-test-folder', $folder->url_key);
    }

    public function test_makes_directories_on_created(): void
    {
        $folder = Folder::factory()->inRoot()->create([
            'name' => 'Test Folder',
        ]);

        Storage::assertExists('uploads/test-folder');
        Storage::assertExists('files/test-folder');
        Storage::assertExists('thumbnails/test-folder');
    }

    public function test_soft_deletion(): void
    {
        $parent = Folder::factory()->inRoot()->create([
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
        Storage::assertExists('thumbnails/test-folder');

        $parent->restore();

        $this->assertNotSoftDeleted($parent);
        $this->assertNotSoftDeleted($child);
        $this->assertSoftDeleted($deletedChild);
        $this->assertNotSoftDeleted($entry);
        $this->assertSoftDeleted($deletedEntry);
        Storage::assertExists('uploads/test-folder');
        Storage::assertExists('files/test-folder');
        Storage::assertExists('thumbnails/test-folder');
    }

    public function test_force_deletion(): void
    {
        $parent = Folder::factory()->inRoot()->create([
            'name' => 'Test Folder',
        ]);
        $child = Folder::factory()->for($parent)->create();
        $entry = Entry::factory()->for($parent)->create();

        $parent->forceDelete();

        $this->assertModelMissing($child);
        $this->assertModelMissing($entry);
        Storage::assertMissing('uploads/test-folder');
        Storage::assertMissing('files/test-folder');
        Storage::assertMissing('thumbnails/test-folder');
    }

    public function test_prune(): void
    {
        $deleted = Folder::factory()->inRoot()->create();
        $implicitlyDeleted = Folder::factory()->inRoot()->create();
        $notDeleted = Folder::factory()->inRoot()->create();

        $deleted->delete();
        $implicitlyDeleted->implicitlyDelete();

        $this->travel(1)->week();

        Artisan::call('model:prune');

        $this->assertModelMissing($deleted);
        $this->assertSoftDeleted($implicitlyDeleted);
        $this->assertModelExists($notDeleted);
    }
}
