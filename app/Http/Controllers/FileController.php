<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Models\Folder;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends Controller
{
    public function show(Folder $folder, Entry $entry): BinaryFileResponse
    {
        $type = Storage::mimeType($entry->path);

        if ($type === false) {
            throw new \Exception("Entry {$entry->id} has unknown file type");
        }

        $viewableTypes = collect([
            'application/pdf',
            'image/jpeg',
            'image/png',
            'text/plain',
        ]);

        if ($viewableTypes->contains($type)) {
            return response()->file(Storage::path($entry->path));
        }

        return response()->download(Storage::path($entry->path), $entry->name);
    }
}
