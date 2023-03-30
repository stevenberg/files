<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Models\Folder;
use App\Presenters\Trash;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TrashController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(): View
    {
        $this->authorize('admin');

        return view('trash', [
            'presenter' => new Trash,
        ]);
    }

    public function update(): RedirectResponse
    {
        $this->authorize('admin');

        Folder::explicitlyDeleted()->get()->each->forceDelete();
        Entry::explicitlyDeleted()->get()->each->forceDelete();

        return redirect()
            ->route('trash.show')
            ->with('success', 'Trash emptied.')
        ;
    }
}
