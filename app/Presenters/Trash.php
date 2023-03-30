<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Models\Entry;
use App\Models\Folder;
use Illuminate\Support\Collection;

/**
 * @property Collection<int, Breadcrumb> $breadcrumbs
 * @property Collection<int, Item> $items
 */
class Trash extends Presenter
{
    public function __construct()
    {
        $this->breadcrumbs = $this->breadcrumbs();

        $folders = Folder::explicitlyDeleted()->get();
        $entries = Entry::explicitlyDeleted()->get();

        $this->items = (new Collection)
            ->concat($folders)
            ->concat($entries)
            ->sortBy(fn ($i) => $i->name)
            ->values()
            ->mapInto(Item::class)
        ;
    }
}
