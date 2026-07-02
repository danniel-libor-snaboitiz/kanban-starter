<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Placeholder profile page so @mention links resolve.
     */
    public function show(User $user): View
    {
        return view('users.show', ['user' => $user]);
    }
}
