<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Folder;
use Illuminate\Database\Seeder;

class FolderSeeder extends Seeder
{
    public function run(): void
    {
        $root = Folder::root()->first();

        $root->folders()->create([
            'name' => 'D&D',
            'path_key' => 'dnd',
        ]);

        $vampire = $root->folders()->create([
            'name' => 'Vampire',
        ]);

        $vampire->folders()->create([
            'name' => 'Images',
        ]);
    }
}
