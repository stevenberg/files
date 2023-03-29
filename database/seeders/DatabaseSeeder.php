<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Artisan::call('db:setup');

        $this->call([
            UserSeeder::class,
            FolderSeeder::class,
            EntrySeeder::class,
            ThumbnailSeeder::class,
        ]);
    }
}
