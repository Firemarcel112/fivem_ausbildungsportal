<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {

        if (Auth::user()?->can('usermanagement.store')) {
            return true;
        }

        return config('app.registration_allowed');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'username' => ['required', 'string', 'max:255', 'unique:users,name'],
            'default_fraction' => ['required', 'integer', 'exists:fractions,fraction_id'],
            'fraction.*' => ['integer', 'exists:fractions,fraction_id'],
        ];

        if (!Auth::user()?->can('usermanagement.store')) {
            $rules['password'] = ['required', 'string', 'min:8'];
        }
        return $rules;
    }
}
