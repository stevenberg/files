<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Folder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\File;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Folder $folder,
        public string $path,
        public string $name,
        ) {
    }

    public function handle(): void
    {
        $file = new File(Storage::path($this->path));
        $path = Storage::putFile($this->folder->filesPath, $file);

        $entry = $this->folder->entries()->create([
            'name' => $this->name,
            'path' => $path,
        ]);

        Storage::delete($this->path);

        CreateThumbnail::dispatch($entry);
    }
}
