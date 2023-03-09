<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Thumbnail;
use App\Values\Thumbnails\Shape;
use App\Values\Thumbnails\Size;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ThumbnailController extends Controller
{
    public function show(Thumbnail $thumbnail, Shape $shape, Size $size): BinaryFileResponse
    {
        return response()->file(Storage::path($thumbnail->path($shape, $size)));
    }
}
