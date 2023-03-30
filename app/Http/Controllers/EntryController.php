<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Jobs\ProcessUpload;
use App\Models\Entry;
use App\Models\Folder;
use App\Presenters\Entries\Create;
use App\Presenters\Entries\Show;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EntryController extends Controller
{
    public function create(Folder $folder): View
    {
        $this->authorize('create', [Entry::class, $folder]);

        return view('entries.create', [
            'presenter' => new Create($folder),
        ]);
    }

    public function store(Request $request, Folder $folder): RedirectResponse
    {
        $this->authorize('create', [Entry::class, $folder]);

        $request->validate([
            'name' => [
                'required',
                Rule::unique('entries')->where('folder_id', $folder->id),
            ],
            'file' => 'required|file',
        ]);

        /** @var UploadedFile */
        $file = $request->file('file');
        /** @var string */
        $name = $request->input('name');

        if ($file->isValid()) {
            $path = $file->store($folder->uploadsPath);

            if ($path === false) {
                return redirect()
                    ->route('folders.show', $folder)
                    ->with('failure', 'Something went wrong with the upload.')
                ;
            }

            ProcessUpload::dispatchSync($folder, $path, $name);

            return redirect()
                ->route('folders.show', $folder)
                ->with('success', "File “{$name}” uploaded.")
            ;
        }

        return redirect()
            ->route('folders.show', $folder)
            ->with('failure', 'Something went wrong with the upload.')
        ;
    }

    public function show(Folder $folder, Entry $entry): View
    {
        $this->authorize('admin');

        return view('entries.show', [
            'presenter' => new Show($entry),
        ]);
    }

    public function destroy(Folder $folder, Entry $entry): RedirectResponse
    {
        if ($entry->trashed()) {
            $this->authorize('forceDelete', $entry);
            $entry->forceDelete();

            return redirect()
                ->route('trash.show')
                ->with('success', "File “{$entry->name}” permanently deleted.")
            ;
        } else {
            $this->authorize('delete', $entry);
            $entry->delete();

            return redirect()
                ->route('folders.show', $entry->folder)
                ->with('success', "Folder “{$entry->name}” deleted.")
            ;
        }
    }

    public function restore(Folder $folder, Entry $entry): RedirectResponse
    {
        $this->authorize('restore', $entry);

        $entry->restore();

        return redirect()
            ->route('folders.show', $entry->folder)
            ->with('success', "File “{$entry->name}” restored.")
        ;
    }
}
