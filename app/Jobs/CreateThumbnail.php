<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Entry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class CreateThumbnail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Entry $entry)
    {
    }

    public function handle(): void
    {
        $this->entry->thumbnail()->forceDelete();

        $type = Storage::mimeType($this->entry->path);

        $job = match ($type) {
            'image/jpeg', 'image/png' => new CreateImageThumbnail($this->entry),
            'application/pdf' => new CreatePdfThumbnail($this->entry),
            default => null,
        };

        if (! is_null($job)) {
            dispatch($job);
        }
    }
}
