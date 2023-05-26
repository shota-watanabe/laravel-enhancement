<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSectionRequest;
use App\Http\Requests\UpdateSectionRequest;
use App\Models\Company;
use App\Models\Section;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Company $company)
    {
        $this->authorize('viewAny', [Section::class, $company]);

        $company->load(['sections']);

        return view('companies.sections.index', compact('company'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Company $company)
    {
        $this->authorize('create', [Section::class, $company]);

        return view('companies.sections.create', compact('company'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSectionRequest $request, Company $company)
    {
        $this->authorize('create', [Section::class, $company]);

        $company->sections()->create($request->validated());

        return redirect()->route('companies.sections.index', compact('company'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company, Section $section)
    {
        $this->authorize('view', [$section, $company]);

        return view('companies.sections.show', compact('company', 'section'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company, Section $section)
    {
        $this->authorize('update', [$section, $company]);

        return view('companies.sections.edit', compact('company', 'section'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSectionRequest $request, Company $company, Section $section)
    {
        $this->authorize('update', [$section, $company]);

        $section->update($request->validated());

        return redirect()->route('companies.sections.show', compact('company', 'section'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company, Section $section)
    {
        $this->authorize('delete', [$section, $company]);

        $section->delete();

        return redirect()->route('companies.sections.index', compact('company'));
    }
}
