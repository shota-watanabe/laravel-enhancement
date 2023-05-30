<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(): View
    {
        if (Auth::user()->isAdmin()) {
            $users = User::paginate();
        } else {
            $users = Auth::user()->company->users()->paginate();
        }
        return view('users.index', compact('users'));
    }
}
