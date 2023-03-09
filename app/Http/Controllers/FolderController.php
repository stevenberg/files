<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Presenters\Folders\Index;
use App\Presenters\Folders\Show;
use Illuminate\View\View;

class FolderController extends Controller
{
    public function index(): View
    {
        return view('folders.index', [
            'presenter' => new Index,
        ]);
    }

    public function show(Folder $folder): View
    {
        return view('folders.show', [
            'presenter' => new Show($folder),
        ]);
    }
}
