<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Models\Entry;
use App\Models\Folder;
use Illuminate\Support\Collection;

/**
 * @property Collection<int, mixed> $ancestors
 * @property Collection<int, Item> $items
 */
class Trash extends Presenter
{
    public function __construct()
    {
        $this->ancestors = new Collection;
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
