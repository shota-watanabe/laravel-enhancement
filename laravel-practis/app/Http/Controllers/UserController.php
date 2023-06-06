<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    use RefreshDatabase;
    use WithFaker;

    public function index(Request $request): View
    {
        $users = User::query()
                 ->with(['company', 'sections'])
                 ->isNotAdmin($request)
                 ->searchUser($request)
                 ->searchCompany($request)
                 ->searchSection($request)
                 ->paginate()->withQueryString();

        return view('users.index', compact('users'));
    }

}
