<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Thumbnail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ThumbnailsFixPaths extends Command
{
    protected $signature = 'thumbnails:fix_paths';

    protected $description = 'Fix old thumbnail paths';

    public function handle(): void
    {
        $thumbnails = Thumbnail::all()
            ->reject(
                fn ($t) => Str::startsWith($t->path_template, $t->entry->thumbnailsPath)
            )
        ;

        $this->withProgressBar($thumbnails, function (Thumbnail $thumbnail) {
            $oldPaths = $thumbnail->paths();

            $name = Str::beforeLast(basename($thumbnail->entry->path), '.');
            $thumbnail->path_template = "{$thumbnail->entry->thumbnailsPath}/{$name}_[SHAPE]_[SIZE].png";

            $newPaths = $thumbnail->paths();

            Storage::makeDirectory($thumbnail->entry->thumbnailsPath);

            $oldPaths->zip($newPaths)->eachSpread(function ($old, $new) {
                Storage::copy($old, $new);
            });

            $thumbnail->save();

            $oldPaths->each(function ($path) {
                Storage::delete($path);
            });
        });
    }
}
