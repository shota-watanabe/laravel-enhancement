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
        if (Auth::user()->isAdmin()) {
            if ($searchType === 'user') {
                $users = User::with(['company', 'sections'])->searchUser($searchKeyword)->paginate()->withQueryString();
            } elseif ($searchType === 'company') {
                $users = User::with(['company', 'sections'])->searchCompany($searchKeyword)->paginate()->withQueryString();
            } elseif ($searchType === 'section') {
                $users = User::with(['company', 'sections'])->searchSection($searchKeyword)->paginate()->withQueryString();
            } else {
                $users = User::with(['company', 'sections'])->paginate()->withQueryString();
            }
        } else {
            if ($searchType === 'user') {
                $users = User::with(['company', 'sections'])->searchUser($searchKeyword)->paginate()->withQueryString();
            } elseif ($searchType === 'section') {
                $users = User::with(['company', 'sections'])->searchSection($searchKeyword)->paginate()->withQueryString();
            } else {
                $users = Auth::user()->company->users()->with(['company', 'sections'])->paginate()->withQueryString();
            }
        }

        return view('users.index', compact('users'));
    }

}
