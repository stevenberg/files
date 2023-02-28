<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Entry;
use App\Models\Thumbnail;
use App\Values\Thumbnails\Shape;
use App\Values\Thumbnails\Size;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;

class CreateImageThumbnail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $pathTemplate;

    public function __construct(public Entry $entry)
    {
        $name = Str::beforeLast(basename($this->entry->path), '.');
        $this->pathTemplate = "{$this->entry->folder->thumbnailsPath}/{$name}_[SHAPE]_[SIZE].png";
    }

    public function handle(): void
    {
        foreach (Size::cases() as $size) {
            $this->resizeOriginal($size);
            $this->resizeSquare($size);
        }

        $image = Image::load(Storage::path($this->entry->path));

        $this->entry->thumbnail()->create([
            'path_template' => $this->pathTemplate,
            'width' => $image->getWidth(),
            'height' => $image->getHeight(),
        ]);
    }

    /** @return array<int, mixed> */
    private function prepare(Shape $shape, Size $size): array
    {
        $image = Image::load(Storage::path($this->entry->path));

        return [
            $image,
            min($size->intValue(), $image->getWidth()),
            Thumbnail::makePath($this->pathTemplate, $shape, $size),
        ];
    }

    private function resizeOriginal(Size $size): void
    {
        [$image, $width, $path] = $this->prepare(Shape::Original, $size);

        $image
            ->manipulate(fn ($m) => $m->width($width))
            ->save(Storage::path($path))
        ;
    }

    private function resizeSquare(Size $size): void
    {
        [$image, $width, $path] = $this->prepare(Shape::Square, $size);

        $image
            ->manipulate(fn ($m) => $m->fit(Manipulations::FIT_FILL, $width, $width))
            ->save(Storage::path($path))
        ;
    }
}
