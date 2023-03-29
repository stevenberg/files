<?php

declare(strict_types=1);

namespace App\Presenters;

use Illuminate\Support\Collection;

abstract class Presenter
{
    /** @var array<string, mixed> */
    private $properties = [];

    /** @var Collection<int, Breadcrumb> */
    private Collection $breadcrumbs;

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

    /** @return Collection<int, Breadcrumb> */
    public function breadcrumbs(): Collection
    {
        if (! isset($this->ancestors)) {
            return new Collection;
        }

        if (! isset($this->breadcrumbs)) {
            $this->breadcrumbs = $this
                ->ancestors
                ->map(fn ($f) => new Breadcrumb($f->name, route('folders.show', $f)))
                ->prepend(new Breadcrumb(config('app.name'), route('home')))
            ;
        }

        return $this->breadcrumbs;
    }
}
