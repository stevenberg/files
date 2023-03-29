<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands;

use Tests\TestCase;

class DbSetupTest extends TestCase
{
    public function test_command(): void
    {
        $this->artisan('db:setup')->assertSuccessful();

        $this->assertDatabaseHas('folders', [
            'folder_id' => null,
            'name' => config('app.name'),
            'path_key' => '',
        ]);
    }
}
