<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        if (Auth::user()->isAdmin()) {
            if ($request->user_name) {
                $users = User::searchUser($request->user_name)->paginate();
            } elseif ($request->company_name){
                $users = User::searchCompany($request->company_name)->paginate();
            } elseif ($request->section_name) {
                $users = User::searchSection($request->section_name)->paginate();
            } else {
                $users = User::paginate();
            }
        } else {
            if ($request->user_name) {
                $users = User::searchUser($request->user_name)->paginate();
            } elseif ($request->section_name) {
                $users = User::query()->searchSection($request->section_name)->paginate();
            } else {
                $users = Auth::user()->company->users()->paginate();
            }
        }


        return view('users.index', compact('users'));
    }

}
