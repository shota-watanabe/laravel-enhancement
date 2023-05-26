<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSectionUserRequest;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class SectionUserController extends Controller
{
    public function store(StoreSectionUserRequest $request, Section $section): RedirectResponse
    {
        $section->users()->attach($request->user_id);

        $company = $section->company;

        return redirect()->route('companies.sections.show', compact('company', 'section'));
    }

    public function destroy(Section $section, User $user): RedirectResponse
    {
        $section->users()->detach($user->id);

        $company = $section->company;

        return redirect()->route('companies.sections.show', compact('company', 'section'));
    }
}
