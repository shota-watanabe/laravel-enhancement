<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        session(['keyword' => $request->search_keyword]);
        if (Auth::user()->isAdmin()) {
            if ($request->search_type === 'user') {
                $users = User::searchUser($request->search_keyword)->paginate()->withQueryString();
            } elseif ($request->search_type === 'company') {
                $users = User::searchCompany($request->search_keyword)->paginate()->withQueryString();
            } elseif ($request->search_type === 'section') {
                $users = User::searchSection($request->search_keyword)->paginate()->withQueryString();
            } else {
                $users = User::paginate()->withQueryString();
            }
        } else {
            if ($request->search_type === 'user') {
                $users = User::searchUser($request->search_keyword)->paginate()->withQueryString();
            } elseif ($request->search_type === 'section') {
                $users = User::query()->searchSection($request->search_keyword)->paginate()->withQueryString();
            } else {
                $users = Auth::user()->company->users()->paginate()->withQueryString();
            }
        }

        Session::put('users', $users);

        return view('users.index', compact('users'));
    }

}
