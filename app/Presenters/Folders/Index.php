<?php

declare(strict_types=1);

namespace App\Presenters\Folders;

use App\Models\Folder;
use App\Presenters\Presenter;
use Illuminate\Support\Collection;

/**
 * @property string $name
 * @property Collection<int, Folder> $folders
 */
class Index extends Presenter
{
    public function __construct()
    {
        $this->name = config('app.name');
        $this->folders = Folder::root()->get();
    }
}
