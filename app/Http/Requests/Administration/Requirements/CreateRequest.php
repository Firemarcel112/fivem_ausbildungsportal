<?php

namespace App\Http\Requests\Administration\Requirements;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()?->can('administration.qualifications.edit') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'fraction_id' => [
                'required',
                'integer',
                'exists:fractions,fraction_id',
            ],
            'rank' => [
                'nullable',
                'integer',
            ],
        ];
    }
}
