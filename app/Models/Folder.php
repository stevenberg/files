<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Folder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'path_key',
        'url_key',
        'implicitly_deleted',
    ];

    protected $casts = [
        'implicitly_deleted' => 'boolean',
    ];

    /** @return BelongsTo<self, self> */
    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    /** @return HasMany<self> */
    public function folders(): HasMany
    {
        return $this->hasMany(Folder::class);
    }

    /** @return HasMany<Entry> */
    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class);
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('folder_id');
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeImplicitlyDeleted(Builder $query): Builder
    {
        return $query->onlyTrashed()->where('implicitly_deleted', true);
    }

    /** @return Collection<int, self> */
    public function getAncestorsAttribute(): Collection
    {
        $ancestors = new Collection;

        for ($parent = $this->folder; ! is_null($parent); $parent = $parent->folder) {
            $ancestors->prepend($parent);
        }

        return $ancestors;
    }

    public function getUploadsPathAttribute(): string
    {
        $path = $this->ancestors->push($this)->map->path_key->join('/');

        return "uploads/{$path}";
    }

    public function getFilesPathAttribute(): string
    {
        $path = $this->ancestors->push($this)->map->path_key->join('/');

        return "files/{$path}";
    }

    public function getThumbnailsPathAttribute(): string
    {
        $path = $this->ancestors->push($this)->map->path_key->join('/');

        return "thumbnails/{$path}";
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

        static::creating(function (Folder $folder) {
            if (blank($folder->path_key)) {
                $folder->path_key = Str::slug($folder->name);
            }

            if (blank($folder->url_key)) {
                $folder->url_key = $folder->ancestors->push($folder)->map->path_key->join('-');
            }
        });

        static::created(function (Folder $folder) {
            Storage::makeDirectory($folder->uploadsPath);
            Storage::makeDirectory($folder->filesPath);
            Storage::makeDirectory($folder->thumbnailsPath);
        });

        static::deleted(function (Folder $folder) {
            $folder->folders->each->implicitlyDelete();
            $folder->entries->each->implicitlyDelete();
            Storage::deleteDirectory($folder->uploadsPath);
        });

        static::restored(function (Folder $folder) {
            $folder->folders()->implicitlyDeleted()->restore();
            $folder->entries()->implicitlyDeleted()->restore();
            Storage::makeDirectory($folder->uploadsPath);
        });

        static::forceDeleted(function (Folder $folder) {
            Storage::deleteDirectory($folder->uploadsPath);
            Storage::deleteDirectory($folder->filesPath);
            Storage::deleteDirectory($folder->thumbnailsPath);
        });
    }
}
