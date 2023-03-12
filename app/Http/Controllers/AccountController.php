<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(): View
    {
        return view('account.show');
    }
}
