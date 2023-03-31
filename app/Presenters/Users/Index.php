<?php

declare(strict_types=1);

namespace App\Presenters\Users;

use App\Models\User as UserModel;
use App\Presenters\Breadcrumb;
use App\Presenters\Presenter;
use App\Presenters\User;
use Illuminate\Support\Collection;

/**
 * @property Collection<int, Breadcrumb> $breadcrumbs
 * @property Collection<int, User> $users
 */
class Index extends Presenter
{
    public function __construct()
    {
        $this->breadcrumbs = $this->breadcrumbs();
        $this->users = UserModel::orderBy('name')->get()->mapInto(User::class);
    }
}
