<?php

namespace App\Http\Requests;

use App\Models\Company;
use App\Models\Section;
use App\Rules\SectionUniqueRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        /** @var Section $section */
        $section = $this->route('section');

        /** @var Company $company */
        $company = $this->route('company');

        return [
            'name' => ['required', 'string', 'max:255', new SectionUniqueRule($company, $section)],
        ];
    }
}
