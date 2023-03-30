<?php

declare(strict_types=1);

namespace App\Presenters\Folders;

use App\Models\Folder;
use App\Presenters\Breadcrumb;
use App\Presenters\Entry;
use App\Presenters\Presenter;
use Illuminate\Support\Collection;

/**
 * @property string $name
 * @property Collection<int, Breadcrumb> $breadcrumbs
 * @property Collection<int, Folder> $folders
 * @property Collection<int, Entry> $entries
 */
class Show extends Presenter
{
    public function __construct(public Folder $folder)
    {
        $this->name = $this->folder->name;
        $this->breadcrumbs = $this->breadcrumbs($this->folder->ancestors);
        $this->folders = $this->folder->folders;
        $this->entries = $this
            ->folder
            ->entries
            ->mapInto(Entry::class)
        ;
    }
}
