<?php

declare(strict_types=1);

namespace App\Presenters\Entries;

use App\Models\Folder;
use App\Presenters\Presenter;
use Illuminate\Support\Collection;

/**
 * @property Collection<int, Folder> $ancestors
 */
class Create extends Presenter
{
    public function __construct(public Folder $folder)
    {
        $this->ancestors = $this->folder->ancestors;
        if (! $this->folder->isRoot) {
            $this->ancestors->push($this->folder);
        }
    }
}
