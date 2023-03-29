<?php

declare(strict_types=1);

namespace App\Presenters\Folders;

use App\Models\Folder;
use App\Presenters\EntryPresenter;
use App\Presenters\Presenter;
use Illuminate\Support\Collection;

/**
 * @property string $name
 * @property Collection<int, Folder> $ancestors
 * @property Collection<int, Folder> $folders
 * @property Collection<int, EntryPresenter> $entries
 */
class Show extends Presenter
{
    public function __construct(public Folder $folder)
    {
        $this->name = $this->folder->name;
        $this->ancestors = $this->folder->ancestors;
        $this->folders = $this->folder->folders;
        $this->entries = $this
            ->folder
            ->entries
            ->mapInto(EntryPresenter::class)
        ;
    }
}
