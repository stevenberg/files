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
use Spatie\PdfToImage\Pdf;

class CreatePdfThumbnail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $temporaryPath;

    private string $pathTemplate;

    public function __construct(public Entry $entry)
    {
        $name = Str::beforeLast(basename($this->entry->path), '.');
        $this->temporaryPath = "{$this->entry->thumbnailsPath}/{$name}.png";
        $this->pathTemplate = "{$this->entry->thumbnailsPath}/{$name}_[SHAPE]_[SIZE].png";
    }

    public function handle(): void
    {
        $pdf = new Pdf(Storage::path($this->entry->path));
        $pdf
            ->width(1500)
            ->setOutputFormat('png')
            ->saveImage(Storage::path($this->temporaryPath))
        ;

        $image = Image::load(Storage::path($this->temporaryPath));

        $image
            ->manipulate(fn ($m) => $m->border(6, 'dddddd', Manipulations::BORDER_OVERLAY))
            ->save(Storage::path($this->temporaryPath))
        ;

        foreach (Size::cases() as $size) {
            $this->resizeOriginal($size);
            $this->resizeSquare($size);
        }

        $this->entry->thumbnail()->create([
            'path_template' => $this->pathTemplate,
            'width' => $image->getWidth(),
            'height' => $image->getHeight(),
        ]);

        Storage::delete($this->temporaryPath);
    }

    private function resizeOriginal(Size $size): void
    {
        $path = Thumbnail::makePath($this->pathTemplate, Shape::Original, $size);

        Image::load(Storage::path($this->temporaryPath))
            ->manipulate(fn ($m) => $m->width($size->intValue()))
            ->save(Storage::path($path))
        ;
    }

    private function resizeSquare(Size $size): void
    {
        $path = Thumbnail::makePath($this->pathTemplate, Shape::Square, $size);

        Image::load(Storage::path($this->temporaryPath))
            ->manipulate(fn ($m) => $m->fit(Manipulations::FIT_FILL, $size->intValue(), $size->intValue()))
            ->save(Storage::path($path))
        ;
    }
}
