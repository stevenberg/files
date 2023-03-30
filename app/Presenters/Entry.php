<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Models\Entry as EntryModel;
use App\Models\Folder;
use App\Models\Thumbnail;
use Illuminate\Support\Facades\Storage;

/**
 * @property Folder|null $folder
 * @property string $name
 * @property Thumbnail $thumbnail
 * @property string $icon
 */
class Entry extends Presenter
{
    public function __construct(public EntryModel $model)
    {
        $this->folder = $this->model->folder;
        $this->name = $this->model->name;
        $this->thumbnail = $this->model->thumbnail;

        $type = Storage::mimeType($this->model->path);

        if ($type === false) {
            throw new \Exception("Entry {$this->model->id} has unknown file type");
        }

        $this->icon = match ($type) {
            'application/pdf' => 'file-pdf',
            'image/jpeg', 'image/png' => 'file-image',
            'text/plain' => 'file-lines',
            default => 'file',
        };
    }
}
