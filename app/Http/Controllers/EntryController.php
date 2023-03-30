<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Jobs\ProcessUpload;
use App\Models\Entry;
use App\Models\Folder;
use App\Presenters\Entries\Create;
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
}
