<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class Folder extends Model
{
    use HasFactory, Prunable, SoftDeletes;

    protected $fillable = [
        'restricted',
        'name',
        'path_key',
        'url_key',
        'implicitly_deleted',
    ];

    protected $casts = [
        'restricted' => 'boolean',
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

    /** @return BelongsToMany<User> */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
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

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeExplicitlyDeleted(Builder $query): Builder
    {
        return $query->onlyTrashed()->where('implicitly_deleted', false);
    }

    /** @return Collection<int, self> */
    public function getAncestorsAttribute(): Collection
    {
        $root = Folder::root()->firstOrFail();

        $ancestors = new Collection;

        if ($this->is($root)) {
            return $ancestors;
        }

        for ($parent = $this->folder; ! $parent?->is($root); $parent = $parent?->folder) {
            $ancestors->prepend($parent);
        }

        return $ancestors;
    }

    public function getIsRootAttribute(): bool
    {
        return is_null($this->folder_id);
    }

    public function getIsRestrictedAttribute(): bool
    {
        return $this->restricted || $this->ancestors->some->restricted;
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

    /** @return Builder<self> */
    public function prunable(): Builder
    {
        return static::explicitlyDeleted()->where('deleted_at', '<=', now()->subWeek());
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

            if (! $folder->implicitly_deleted) {
                Storage::deleteDirectory($folder->uploadsPath);
            }
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
