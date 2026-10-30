<?php

namespace App\Observers;

use App\Models\Logs;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    public function created(User $user) : void
    {
        Logs::write(
            'create',
            'User',
            Auth::user()->name . " created a new user account for {$user->name}."
        );
    }

    public function updated(User $user) : void
    {
        Logs::write(
            'edit',
            'User',
            Auth::user()->name . " update details of {$user->name}."
        );
    }
}
