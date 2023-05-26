<?php

namespace App\Rules;

use App\Models\Company;
use App\Models\Section;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class SectionUniqueRule implements ValidationRule
{
    private ?Section $section;

    private Company $company;

    public function __construct(Company $company, Section $section = null)
    {
        $this->company = $company;
        $this->section = $section;
    }

    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param \Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $sectionExists = Section::query()
            ->when($this->section, function ($query) {
                $query->whereKeyNot($this->section->getKey());
            })
            ->where([
                ['name', $value],
                ['company_id', $this->company->id],
            ])
            ->exists();

        if ($sectionExists) {
            $fail('その部署名はすでに登録済みです。');
        }
    }
}
