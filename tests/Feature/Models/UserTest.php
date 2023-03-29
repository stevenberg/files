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
        Storage::fake('public');
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

    public function test_active_scope(): void
    {
        $pending = User::factory()->pending()->create();
        $viewer = User::factory()->viewer()->create();
        $admin = User::factory()->admin()->create();

        $active = User::active()->get();

        $this->assertFalse($active->contains($pending));
        $this->assertTrue($active->contains($viewer));
        $this->assertTrue($active->contains($admin));
    }
}
