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
    public function __construct(public Folder $model)
    {
        $this->name = $this->model->name;
        $this->folders = $this->model->folders;
        $this->entries = $this
            ->model
            ->entries
            ->mapInto(EntryPresenter::class)
        ;
    }
}
