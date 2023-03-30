<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Entry;
use App\Models\Folder;
use App\Models\Thumbnail;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EntryTest extends TestCase
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

        $entry = new Entry([
            'restricted' => true,
            'name' => 'test',
            'path' => 'test',
            'url_key' => 'test',
            'implicitly_deleted' => true,
            'folder_id' => 1,
        ]);

        $this->assertSame(true, $entry->restricted);
        $this->assertSame('test', $entry->name);
        $this->assertSame('test', $entry->path);
        $this->assertSame('test', $entry->url_key);
        $this->assertTrue($entry->implicitly_deleted);
        $this->assertNull($entry->folder_id);
    }

    public function test_folder_relationship(): void
    {
        $folder = Folder::factory()->inRoot()->create();
        $entry = Entry::factory()->make();

        $entry->folder()->associate($folder);

        $this->assertTrue($entry->folder->is($folder));
    }

    public function test_thumbnail_relationship(): void
    {
        $entry = Entry::factory()->for(Folder::factory()->inRoot())->create();
        $thumbnail = Thumbnail::factory()->make();

        $entry->thumbnail()->save($thumbnail);

        $this->assertTrue($entry->thumbnail->is($thumbnail));
    }

    public function test_default_thumbnail(): void
    {
        $entry = Entry::factory()->for(Folder::factory()->inRoot())->create();

        $this->assertInstanceOf(Thumbnail::class, $entry->thumbnail);
        $this->assertFalse($entry->thumbnail->exists);
    }

    public function test_users_relationship(): void
    {
        $entry = Entry::factory()->for(Folder::factory()->inRoot())->create();
        $user = User::factory()->create();

        $entry->users()->attach($user);

        $this->assertTrue($entry->users->contains($user));
    }

    public function test_implicitly_deleted_scope(): void
    {
        $entries = Entry::factory()
            ->count(3)
            ->for(Folder::factory()->inRoot())
            ->state(new Sequence(
                ['implicitly_deleted' => false],
                ['implicitly_deleted' => false],
                ['implicitly_deleted' => true],
            ))
            ->create()
        ;

        $entries->skip(1)->each->delete();

        $implicitlyDeleted = Entry::implicitlyDeleted()->get();

        $this->assertFalse($implicitlyDeleted->contains($entries[0]));
        $this->assertFalse($implicitlyDeleted->contains($entries[1]));
        $this->assertTrue($implicitlyDeleted->contains($entries[2]));
    }

    public function test_explicitly_deleted_scope(): void
    {
        $entries = Entry::factory()
            ->count(3)
            ->for(Folder::factory()->inRoot())
            ->state(new Sequence(
                ['implicitly_deleted' => false],
                ['implicitly_deleted' => false],
                ['implicitly_deleted' => true],
            ))
            ->create()
        ;

        $entries->skip(1)->each->delete();

        $explicitlyDeleted = Entry::explicitlyDeleted()->get();

        $this->assertFalse($explicitlyDeleted->contains($entries[0]));
        $this->assertTrue($explicitlyDeleted->contains($entries[1]));
        $this->assertFalse($explicitlyDeleted->contains($entries[2]));
    }

    public function test_ancestors(): void
    {
        $parent = Folder::factory()->inRoot()->create();
        $child = Folder::factory()->for($parent)->create();
        $entry = Entry::factory()->for($child)->create();

        $expected = [
            $parent,
            $child,
        ];

        $ancestors = $entry->ancestors;

        $ancestors->zip($expected)->eachSpread(function ($folder, $expected) {
            $this->assertTrue($folder->is($expected));
        });
    }

    public function test_is_restricted(): void
    {
        $entry = Entry::factory()->for(Folder::factory()->inRoot())->create();

        $this->assertFalse($entry->isRestricted);

        $entry->restricted = true;

        $this->assertTrue($entry->isRestricted);

        $entry->restricted = false;
        $entry->folder->restricted = true;

        $this->assertTrue($entry->isRestricted);
    }

    public function test_thumbnails_path(): void
    {
        $folder = Folder::factory()->inRoot()->create([
            'name' => 'Test Folder',
        ]);
        $entry = Entry::factory()->for($folder)->create([
            'name' => 'Test Entry',
        ]);

        $this->assertSame('thumbnails/test-folder/test-entry', $entry->thumbnailsPath);
    }

    public function test_implicitly_delete(): void
    {
        $entry = Entry::factory()->for(Folder::factory()->inRoot())->create();

        $entry->implicitlyDelete();

        $this->assertSoftDeleted($entry);
        $this->assertTrue($entry->implicitly_deleted);
    }

    public function test_ordered_by_name(): void
    {
        $entries = Entry::factory()
            ->count(3)
            ->for(Folder::factory()->inRoot())
            ->state(new Sequence(
                ['name' => 'B'],
                ['name' => 'A'],
                ['name' => 'C'],
            ))
            ->create()
        ;

        $expected = collect([
            $entries[1],
            $entries[0],
            $entries[2],
        ]);

        $entries = Entry::all();

        $entries->zip($expected)->eachSpread(function ($entry, $expected) {
            $this->assertTrue($entry->is($expected));
        });
    }

    public function test_sets_url_key_on_creating(): void
    {
        $entry = Entry::factory()->for(Folder::factory()->inRoot())->create([
            'name' => 'Test Entry',
        ]);

        $this->assertSame('test-entry', $entry->url_key);
    }

    public function test_makes_directory_on_created(): void
    {
        $folder = Folder::factory()->inRoot()->create([
            'name' => 'Test Folder',
        ]);
        $entry = Entry::factory()->for($folder)->create([
            'name' => 'Test Entry',
        ]);

        Storage::assertExists('thumbnails/test-folder/test-entry');
    }

    public function test_soft_deletion(): void
    {
        $entry = Entry::factory()->for(Folder::factory()->inRoot())->create([
            'name' => 'Test Entry',
        ]);
        $thumbnail = Thumbnail::factory()->for($entry)->create();

        $entry->delete();

        $this->assertSoftDeleted($thumbnail);

        $entry->restore();

        $this->assertNotSoftDeleted($thumbnail);
    }

    public function test_force_deletion(): void
    {
        $entry = Entry::factory()->for(Folder::factory()->inRoot())->create();
        $thumbnail = Thumbnail::factory()->for($entry)->create();

        Storage::assertExists($entry->path);

        $entry->forceDelete();

        $this->assertModelMissing($thumbnail);
        Storage::assertMissing($entry->path);
        Storage::assertMissing($entry->thumbnailsPath);
    }

    public function test_prune(): void
    {
        $deleted = Entry::factory()->for(Folder::factory()->inRoot())->create();
        $implicitlyDeleted = Entry::factory()->for(Folder::factory()->inRoot())->create();
        $notDeleted = Entry::factory()->for(Folder::factory()->inRoot())->create();

        $deleted->delete();
        $implicitlyDeleted->implicitlyDelete();

        $this->travel(1)->week();

        Artisan::call('model:prune');

        $this->assertModelMissing($deleted);
        $this->assertSoftDeleted($implicitlyDeleted);
        $this->assertModelExists($notDeleted);
    }
}
