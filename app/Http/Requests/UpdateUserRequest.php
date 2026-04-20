<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use App\Models\Fraction;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $updated_user = $this->route('user');

        $fraction = new Fraction;
        $user = new User;

        return [
            'first_name' => [
                'required',
                'string',
                'max:255',
            ],
            'last_name' => [
                'required',
                'string',
                'max:255',
            ],
            'date_of_birth' => [
                'required',
                'date',
            ],
            'birth_location' => [
                'required',
                'string',
                'max:255',
            ],
            'gender' => [
                'required',
                Rule::enum(Gender::class),
            ],
            'default_fraction' => [
                'required',
                'integer',
                Rule::exists($fraction->getTable(), $fraction->getKeyName()),
            ],
            'fraction.*' => [
                'integer',
                Rule::exists($fraction->getTable(), $fraction->getKeyName()),
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
            ],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique($user->getTable(), 'name')->ignore($updated_user),
            ],
        ];
    }
}
