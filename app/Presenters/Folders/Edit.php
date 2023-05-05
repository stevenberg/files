<?php

declare(strict_types=1);

namespace App\Presenters\Folders;

use App\Models\Folder;
use App\Models\User;
use App\Presenters\Breadcrumb;
use App\Presenters\Presenter;
use Illuminate\Support\Collection;

/**
 * @property string $title
 * @property Collection<int, Breadcrumb> $breadcrumbs
 * @property Collection<int, User> $users
 * @property Collection<int, int> $viewers
 */
class Edit extends Presenter
{
    public function __construct(public Folder $folder)
    {
        $this->title = "Edit {$this->folder->name}";
        $this->breadcrumbs = $this->breadcrumbs(
            $this->folder->ancestors->concat([$this->folder])
        );
        $this->users = User::viewer()->select('id', 'name')->get();
        $this->viewers = $this->folder->users->map->id;
    }
}
