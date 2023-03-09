<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Models\Thumbnail as Model;
use App\Values\Thumbnails\Shape;
use App\Values\Thumbnails\Size;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Component;

class Thumbnail extends Component
{
    public string $name;

    public string $src;

    public int $width;

    public int $height;

    public function __construct(private Model $thumbnail)
    {
        $this->name = $thumbnail->entry->name;
        $this->src = route('thumbnails.show', [
            'thumbnail' => $this->thumbnail,
            'shape' => Shape::Original,
            'size' => Size::S250,
        ]);
        $this->width = $thumbnail->width;
        $this->height = $thumbnail->height;
    }

    public function srcset(Shape $shape): string
    {
        return collect(Size::cases())
            ->map(function ($size) use ($shape) {
                $url = route('thumbnails.show', [
                    'thumbnail' => $this->thumbnail,
                    'shape' => $shape,
                    'size' => $size,
                ]);
                // $url = Storage::disk('public')->url($this->thumbnail->path($shape, $size));

                return "{$url} {$size->value}w";
            })
            ->join(', ')
        ;
    }

    public function render(): View
    {
        return view('components.thumbnail');
    }
}
