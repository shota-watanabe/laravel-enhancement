<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\Section;
use App\Models\User;

class SectionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Company $company): bool
    {
        return $user->company_id === $company->id;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Section $section, Company $company): bool
    {
        return $user->company_id === $company->id && $company->id === $section->company_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Company $company): bool
    {
        return $user->company_id === $company->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Section $section, Company $company): bool
    {
        return $user->company_id === $company->id && $company->id === $section->company_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Section $section, Company $company): bool
    {
        return $user->company_id === $company->id && $company->id === $section->company_id;
    }
}
