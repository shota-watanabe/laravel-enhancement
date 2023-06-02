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
        $searchType = $request->search_type;
        $searchKeyword = $request->search_keyword;
        $user = New User();
        $users = $user->keywordSearch($searchType, $searchKeyword);

        return view('users.index', compact('users'));
    }

}
