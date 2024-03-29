<?php

declare(strict_types=1);

namespace App\Presenters\Folders;

use App\Models\Folder;
use App\Models\User;
use App\Presenters\Breadcrumb;
use App\Presenters\Presenter;
use Illuminate\Support\Collection;

/**
 * @property Collection<int, Breadcrumb> $breadcrumbs
 * @property Collection<int, User> $users
 */
class Create extends Presenter
{
    public function __construct(public Folder $folder)
    {
        $ancestors = $this->folder->isRoot
            ? new Collection
            : $this->folder->ancestors->concat([$this->folder]);
        $this->breadcrumbs = $this->breadcrumbs($ancestors);
        $this->users = User::viewer()->select('id', 'name')->get();
    }
}
