<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Entry;
use App\Models\Folder;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EntryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake();
    }

    public function test_fillable_attributes(): void
    {
        Model::preventSilentlyDiscardingAttributes(false);

        $entry = new Entry([
            'name' => 'test',
            'path' => 'test',
            'url_key' => 'test',
            'implicitly_deleted' => true,
            'folder_id' => 1,
        ]);

        $this->assertSame('test', $entry->name);
        $this->assertSame('test', $entry->path);
        $this->assertSame('test', $entry->url_key);
        $this->assertTrue($entry->implicitly_deleted);
        $this->assertNull($entry->folder_id);
    }

    public function test_folder_relationship(): void
    {
        $folder = Folder::factory()->create();
        $entry = Entry::factory()->make();

        $entry->folder()->associate($folder);

        $this->assertTrue($entry->folder->is($folder));
    }

    public function test_implicitly_deleted_scope(): void
    {
        $entries = Entry::factory()
            ->count(3)
            ->forFolder()
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

    public function test_ancestors(): void
    {
        $root = Folder::factory()->create();
        $parent = Folder::factory()->for($root)->create();
        $child = Folder::factory()->for($parent)->create();
        $entry = Entry::factory()->for($child)->create();

        $expected = [
            $root,
            $parent,
            $child,
        ];

        $ancestors = $entry->ancestors;

        $ancestors->zip($expected)->eachSpread(function ($folder, $expected) {
            $this->assertTrue($folder->is($expected));
        });
    }

    public function test_implicitly_delete(): void
    {
        $entry = Entry::factory()->forFolder()->create();

        $entry->implicitlyDelete();

        $this->assertSoftDeleted($entry);
        $this->assertTrue($entry->implicitly_deleted);
    }

    public function test_ordered_by_name(): void
    {
        $entries = Entry::factory()
            ->count(3)
            ->forFolder()
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
        $entry = Entry::factory()->forFolder()->create([
            'name' => 'Test Entry',
        ]);

        $this->assertSame('test-entry', $entry->url_key);
    }

    public function test_deletes_path_on_force_deleted(): void
    {
        $entry = Entry::factory()->forFolder()->create([
            'name' => 'Test Entry',
        ]);

        Storage::assertExists($entry->path);

        $entry->forceDelete();

        Storage::assertMissing($entry->path);
    }
}
