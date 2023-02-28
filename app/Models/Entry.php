<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Entry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'path',
        'url_key',
        'implicitly_deleted',
    ];

    protected $casts = [
        'implicitly_deleted' => 'boolean',
    ];

    /** @return BelongsTo<Folder, self> */
    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    /** @return HasOne<Thumbnail> */
    public function thumbnail(): HasOne
    {
        return $this->hasOne(Thumbnail::class)->withDefault();
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeImplicitlyDeleted(Builder $query): Builder
    {
        return $query->onlyTrashed()->where('implicitly_deleted', true);
    }

    /** @return Collection<int, Folder> */
    public function getAncestorsAttribute(): Collection
    {
        return $this->folder->ancestors->push($this->folder);
    }

    public function getRouteKeyName(): string
    {
        return 'url_key';
    }

    public function implicitlyDelete(): bool|null
    {
        $this->update(['implicitly_deleted' => true]);

        return $this->delete();
    }

    protected static function booted(): void
    {
        static::addGlobalScope('ordered', function (Builder $query) {
            $query->orderBy('name');
        });

        static::creating(function (Entry $entry) {
            if (blank($entry->url_key)) {
                $entry->url_key = Str::slug($entry->name);
            }
        });

        static::deleted(function (Entry $entry) {
            optional($entry->thumbnail)->delete();
        });

        static::restored(function (Entry $entry) {
            optional($entry->thumbnail()->onlyTrashed()->first())->restore();
        });

        static::forceDeleted(function (Entry $entry) {
            Storage::delete($entry->path);
        });
    }
}
