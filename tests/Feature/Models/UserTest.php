<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Entry;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake();
        Artisan::call('db:setup');
    }

    public function test_fillable_attributes(): void
    {
        Model::preventSilentlyDiscardingAttributes(false);

        $user = new User([
            'role' => 'admin',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'test',
            'remember_token' => 'test',
            'two_factor_secret' => 'test',
            'two_factor_recovery_codes' => 'test',
            'two_factor_confirmed_at' => now(),
            'email_verified_at' => now(),
        ]);

        $this->assertSame('admin', $user->role);
        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertSame('test', $user->password);
        $this->assertNull($user->remember_token);
        $this->assertNull($user->two_factor_secret);
        $this->assertNull($user->two_factor_recovery_codes);
        $this->assertNull($user->two_factor_confirmed_at);
        $this->assertNull($user->email_verified_at);
    }

    public function test_hidden_attributes(): void
    {
        $user = User::factory()->make();

        $attributes = $user->toArray();

        $this->assertArrayNotHasKey('password', $attributes);
        $this->assertArrayNotHasKey('remember_token', $attributes);
        $this->assertArrayNotHasKey('two_factor_secret', $attributes);
        $this->assertArrayNotHasKey('two_factor_recovery_codes', $attributes);
        $this->assertArrayNotHasKey('two_factor_confirmed_at', $attributes);
    }

    public function test_casts(): void
    {
        $user = User::factory()->make([
            'email_verified_at' => '2023-01-01 00:00:00',
        ]);

        $this->assertInstanceOf(Carbon::class, $user->email_verified_at);
    }

    public function test_folders_relationship(): void
    {
        $user = User::factory()->create();
        $folder = Folder::factory()->inRoot()->create();

        $user->folders()->attach($folder);

        $this->assertTrue($user->folders->contains($folder));
    }

    public function test_entries_relationship(): void
    {
        $user = User::factory()->create();
        $entry = Entry::factory()->for(Folder::factory()->inRoot())->create();

        $user->entries()->attach($entry);

        $this->assertTrue($user->entries->contains($entry));
    }

    public function test_admin_scope(): void
    {
        $admin = User::factory()->admin()->create();
        $viewer = User::factory()->viewer()->create();
        $pending = User::factory()->pending()->create();

        $users = User::admin()->get();

        $this->assertTrue($users->contains($admin));
        $this->assertFalse($users->contains($viewer));
        $this->assertFalse($users->contains($pending));
    }

    public function test_viewer_scope(): void
    {
        $admin = User::factory()->admin()->create();
        $viewer = User::factory()->viewer()->create();
        $pending = User::factory()->pending()->create();

        $users = User::viewer()->get();

        $this->assertFalse($users->contains($admin));
        $this->assertTrue($users->contains($viewer));
        $this->assertFalse($users->contains($pending));
    }

    public function test_is_pending(): void
    {
        $pending = User::factory()->pending()->create();
        $viewer = User::factory()->viewer()->create();
        $admin = User::factory()->admin()->create();

        $this->assertTrue($pending->isPending);
        $this->assertFalse($viewer->isPending);
        $this->assertFalse($admin->isPending);
    }

    public function test_is_viewer(): void
    {
        $pending = User::factory()->pending()->create();
        $viewer = User::factory()->viewer()->create();
        $admin = User::factory()->admin()->create();

        $this->assertFalse($pending->isViewer);
        $this->assertTrue($viewer->isViewer);
        $this->assertFalse($admin->isViewer);
    }

    public function test_is_admin(): void
    {
        $pending = User::factory()->pending()->create();
        $viewer = User::factory()->viewer()->create();
        $admin = User::factory()->admin()->create();

        $this->assertFalse($pending->isAdmin);
        $this->assertFalse($viewer->isAdmin);
        $this->assertTrue($admin->isAdmin);
    }

    public function test_prune(): void
    {
        $pending = User::factory()->pending()->create();
        $viewer = User::factory()->viewer()->create();
        $admin = User::factory()->admin()->create();

        $this->travel(1)->day();

        Artisan::call('model:prune');

        $this->assertModelMissing($pending);
        $this->assertModelExists($viewer);
        $this->assertModelExists($admin);
    }
}
