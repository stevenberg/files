<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, Prunable, SoftDeletes, TwoFactorAuthenticatable;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'role',
        'name',
        'email',
        'password',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /** @return BelongsTomany<Folder> */
    public function folders(): BelongsToMany
    {
        return $this->belongsToMany(Folder::class)->withTimestamps();
    }

    /** @return BelongsTomany<Entry> */
    public function entries(): BelongsToMany
    {
        return $this->belongsToMany(Entry::class)->withTimestamps();
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeAdmin(Builder $query): Builder
    {
        return $query->where('role', 'admin');
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->role === 'pending';
    }

    public function getIsViewerAttribute(): bool
    {
        return $this->role === 'viewer';
    }

    public function getIsAdminAttribute(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * @return Builder<self>
     */
    public function prunable(): Builder
    {
        return static::where('role', 'pending')->where('created_at', '<=', now()->subDay());
    }
}
