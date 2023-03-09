<?php

declare(strict_types=1);

namespace App\Models;

use App\Values\Thumbnails\Shape;
use App\Values\Thumbnails\Size;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @property Entry $entry
 */
class Thumbnail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'path_template',
        'width',
        'height',
    ];

    /** @return BelongsTo<Entry, self> */
    public function entry(): BelongsTo
    {
        return $this->belongsTo(Entry::class);
    }

    /** @return Collection<int, string> */
    public function paths(?Shape $shape = null): Collection
    {
        if (is_null($shape)) {
            return collect(Shape::cases())->flatMap(fn ($s) => $this->paths($s));
        }

        return collect(Size::cases())->map(fn ($s) => $this->path($shape, $s));
    }

    public function path(Shape $shape, Size $size): string
    {
        return static::makePath($this->path_template, $shape, $size);
    }

    public static function makePath(string $template, Shape $shape, Size $size): string
    {
        return Str::of($template)
            ->replace('[SHAPE]', $shape->value)
            ->replace('[SIZE]', $size->value)
            ->toString()
        ;
    }

    protected static function booted(): void
    {
        static::forceDeleted(function (Thumbnail $thumbnail) {
            $thumbnail->paths()->each(
                fn ($p) => Storage::delete($p)
            );
        });
    }
}
