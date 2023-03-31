<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Presenters\Users\Index;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $this->authorize('admin');

        return view('users.index', [
            'presenter' => new Index,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize('admin');

        $user->update(['role' => 'viewer']);

        return redirect()
            ->route('users.index')
            ->with('success', "User “{$user->name}” approved.")
        ;
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('admin');

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', "User “{$user->name}” deleted.")
        ;
    }
}
