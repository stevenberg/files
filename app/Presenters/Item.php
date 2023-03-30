<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Models\Entry;
use App\Models\Folder;
use Illuminate\Support\Facades\Storage;

/**
 * @property string $name
 * @property string $icon
 * @property string $restoreRoute
 * @property string $destroyRoute
 */
class Item extends Presenter
{
    public function __construct(public Entry|Folder $model)
    {
        $this->name = $this
            ->model
            ->ancestors
            ->map->name
            ->push($this->model->name)
            ->join(' / ')
        ;
        $this->icon = $this->icon();
        $this->restoreRoute = $this->restoreRoute();
        $this->destroyRoute = $this->destroyRoute();
    }

    private function icon(): string
    {
        if (is_a($this->model, Folder::class)) {
            return 'folder';
        }

        $type = Storage::mimeType($this->model->path);

        if ($type === false) {
            throw new \Exception("Entry {$this->model->id} has unknown file type");
        }

        return match ($type) {
            'application/pdf' => 'file-pdf',
            'image/jpeg', 'image/png' => 'file-image',
            'text/plain' => 'file-lines',
            default => 'file',
        };
    }

    private function restoreRoute(): string
    {
        return match ($this->model::class) {
            Entry::class => route('folders.entries.restore', [
                'folder' => $this->model->folder,
                'entry' => $this->model,
            ]),
            Folder::class => route('folders.restore', $this->model),
        };
    }

    private function destroyRoute(): string
    {
        return match ($this->model::class) {
            Entry::class => route('folders.entries.destroy', [
                'folder' => $this->model->folder,
                'entry' => $this->model,
            ]),
            Folder::class => route('folders.destroy', $this->model),
        };
    }
}
