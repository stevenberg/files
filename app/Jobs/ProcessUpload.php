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
use Illuminate\Support\Str;

class ProcessUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Folder $folder, public string $path)
    {
    }

    public function handle(): void
    {
        $file = new File(Storage::path($this->path));
        $name = Str::beforeLast($file->getBasename(), '.');

        $path = Storage::putFile($this->folder->filesPath, $file);

        $this->folder->entries()->create([
            'name' => $name,
            'path' => $path,
        ]);

        Storage::delete($this->path);
    }
}
