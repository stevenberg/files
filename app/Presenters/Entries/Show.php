<?php

declare(strict_types=1);

namespace App\Presenters\Entries;

use App\Models\Entry;
use App\Models\Folder;
use App\Presenters\Breadcrumb;
use App\Presenters\Presenter;
use App\Values\Thumbnails\Shape;
use App\Values\Thumbnails\Size;
use Illuminate\Support\Collection;

/**
 * @property Folder $folder
 * @property string $name
 * @property Collection<int, Breadcrumb> $breadcrumbs
 * @property string $thumbnailSrc
 * @property string $thumbnailSrcset
 * @property int $thumbnailWidth
 * @property int $thumbnailHeight
 */
class Show extends Presenter
{
    public function __construct(public Entry $entry)
    {
        $this->folder = $this->entry->folder;
        $this->name = $this->entry->name;
        $this->breadcrumbs = $this->breadcrumbs($this->entry->ancestors);

        $this->thumbnailSrc = route('thumbnails.show', [
            'thumbnail' => $this->entry->thumbnail,
            'shape' => Shape::Original,
            'size' => Size::S1500,
        ]);

        $this->thumbnailSrcset = collect(Size::cases())
            ->map(function ($size) {
                $url = route('thumbnails.show', [
                    'thumbnail' => $this->entry->thumbnail,
                    'shape' => Shape::Original,
                    'size' => $size,
                ]);

                return "{$url} {$size->value}w";
            })
            ->join(', ')
        ;

        $this->thumbnailWidth = $this->entry->thumbnail->width;
        $this->thumbnailHeight = $this->entry->thumbnail->height;
    }
}
