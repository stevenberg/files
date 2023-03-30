<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Models\Folder;
use Illuminate\Support\Collection;

abstract class Presenter
{
    /** @var array<string, mixed> */
    private $properties = [];

    public function __set(string $name, mixed $value): void
    {
        $this->properties[$name] = $value;
    }

    public function __get(string $name): mixed
    {
        return $this->properties[$name] ?? null;
    }

    public function __isset(string $name): bool
    {
        return isset($this->properties[$name]);
    }

    public function __unset(string $name): void
    {
        unset($this->properties[$name]);
    }

    /**
     * @param  Collection<int, Folder>  $ancestors
     * @return Collection<int, Breadcrumb>
     */
    public function breadcrumbs(Collection $ancestors = new Collection): Collection
    {
        return $ancestors
            ->map(fn ($f) => new Breadcrumb($f->name, route('folders.show', $f)))
            ->prepend(new Breadcrumb(config('app.name'), route('home')))
        ;
    }
}
