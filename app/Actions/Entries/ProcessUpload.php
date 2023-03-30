<?php

declare(strict_types=1);

namespace App\Actions\Entries;

use App\Jobs\CreateThumbnail;
use App\Models\Entry;
use App\Models\Folder;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class ProcessUpload
{
    public function __construct(
        public Folder $folder,
        public string $path,
        public string $name,
        ) {
    }

    public function run(): Entry
    {
        $file = new File(Storage::path($this->path));
        $path = Storage::putFile($this->folder->filesPath, $file);

        $entry = $this->folder->entries()->create([
            'name' => $this->name,
            'path' => $path,
        ]);

        Storage::delete($this->path);

        CreateThumbnail::dispatch($entry);

        return $entry;
    }
}
