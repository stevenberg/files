<?php

declare(strict_types=1);

namespace App\Presenters\Folders;

use App\Models\Folder;
use App\Presenters\Presenter;
use Illuminate\Support\Collection;

/**
 * @property Folder $folder
 * @property string $name
 * @property Collection<int, Folder> $folders
 */
class Index extends Presenter
{
    public function __construct()
    {
        $this->folder = Folder::root()->firstOrFail();
        $this->name = $this->folder->name;
        $this->folders = $this->folder->folders;
    }
}
