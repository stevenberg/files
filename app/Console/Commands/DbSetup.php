<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Folder;
use Illuminate\Console\Command;

class DbSetup extends Command
{
    protected $signature = 'db:setup';

    protected $description = 'Initialize the database';

    public function handle(): void
    {
        Folder::withoutEvents(function () {
            Folder::firstOrCreate([
                'name' => config('app.name'),
                'path_key' => '',
                'url_key' => '',
            ]);
        });
    }
}
