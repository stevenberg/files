<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Models\User as UserModel;
use Illuminate\Support\Str;

/**
 * @property string $name
 * @property string $email
 * @property string $role
 * @property bool $isPending
 */
class User extends Presenter
{
    public function __construct(public UserModel $model)
    {
        $this->name = $this->model->name;
        $this->email = $this->model->email;
        $this->role = Str::title($this->model->role);
        $this->isPending = $this->model->isPending;
    }
}
