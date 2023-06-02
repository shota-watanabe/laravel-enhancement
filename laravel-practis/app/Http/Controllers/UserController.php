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
        if (Auth::user()->isAdmin()) {
            $searchType = $request->search_type;
            $searchKeyword = $request->search_keyword;

            if ($searchType === 'user') {
                $users = User::searchUser($searchKeyword)->paginate()->withQueryString();
            } elseif ($searchType === 'company') {
                $users = User::searchCompany($searchKeyword)->paginate()->withQueryString();
            } elseif ($searchType === 'section') {
                $users = User::searchSection($searchKeyword)->paginate()->withQueryString();
            } else {
                $users = User::paginate()->withQueryString();
            }
        } else {
            $searchType = $request->search_type;
            $searchKeyword = $request->search_keyword;

            if ($searchType === 'user') {
                $users = User::searchUser($searchKeyword)->paginate()->withQueryString();
            } elseif ($searchType === 'section') {
                $users = User::query()->searchSection($searchKeyword)->paginate()->withQueryString();
            } else {
                $users = Auth::user()->company->users()->paginate()->withQueryString();
            }
        }

        return view('users.index', compact('users'));
    }

}
