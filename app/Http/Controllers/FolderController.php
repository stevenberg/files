<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Presenters\Folders\Create;
use App\Presenters\Folders\Edit;
use App\Presenters\Folders\Index;
use App\Presenters\Folders\Show;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FolderController extends Controller
{
    public function index(): View
    {
        return view('folders.index', [
            'presenter' => new Index,
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Folder::class);

        $request->validate([
            'folder_id' => 'required|exists:folders,id',
        ]);

        return view('folders.create', [
            'presenter' => new Create(Folder::where('id', $request->folder_id)->firstOrFail()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Folder::class);

        $request->validate([
            'name' => [
                'required',
                Rule::unique('folders')->where('folder_id', $request->folder_id),
            ],
            'folder_id' => 'required|exists:folders,id',
        ]);

        $folder = Folder::where('id', $request->folder_id)
            ->firstOrFail()
            ->folders()
            ->create([
                'name' => $request->name,
            ])
        ;

        return redirect()
            ->route('folders.show', $folder)
            ->with('success', "Folder “{$folder->name}” created.")
        ;
    }

    public function show(Folder $folder): View
    {
        $this->authorize('view', $folder);

        return view('folders.show', [
            'presenter' => new Show($folder),
        ]);
    }

    public function edit(Folder $folder): View
    {
        $this->authorize('update', $folder);

        return view('folders.edit', [
            'presenter' => new Edit($folder),
        ]);
    }

    public function update(Request $request, Folder $folder): RedirectResponse
    {
        $request->validate([
            'name' => [
                'required',
                Rule::unique('folders')->where('folder_id', $folder->folder_id)->ignore($folder->id),
            ],
            'restricted' => 'required|in:0,1',
            'users.*.id' => 'required|exists:users,id',
            'users.*.selected' => 'required|in:0,1',
        ]);

        $folder->update([
            'name' => $request->name,
            'restricted' => (bool) $request->restricted,
        ]);

        /** @var array<int, array{id: string, selected: '0' | '1'}> */
        $users = $request->users;
        $userIds = collect($users)
            ->filter(fn ($u) => $u['selected'] === '1')
            ->map(fn ($u) => $u['id'])
        ;
        $folder->users()->sync($userIds);

        return redirect()
            ->route('folders.show', $folder)
            ->with('success', "Folder “{$folder->name}” updated.")
        ;
    }

    public function destroy(Folder $folder): RedirectResponse
    {
        if ($folder->trashed()) {
            $this->authorize('forceDelete', $folder);
            $folder->forceDelete();

            return redirect()
                ->route('trash.show')
                ->with('success', "Folder “{$folder->name}” permanently deleted.")
            ;
        } else {
            $this->authorize('delete', $folder);
            $folder->delete();

            $response = isset($folder->folder) && $folder->folder->isRoot
                ? redirect()->route('home')
                : redirect()->route('folders.show', $folder->folder);

            return $response->with('success', "Folder “{$folder->name}” deleted.");
        }
    }

    public function restore(Folder $folder): RedirectResponse
    {
        $this->authorize('restore', $folder);

        $folder->restore();

        return redirect()
            ->route('folders.show', $folder)
            ->with('success', "Folder “{$folder->name}” restored.")
        ;
    }
}
