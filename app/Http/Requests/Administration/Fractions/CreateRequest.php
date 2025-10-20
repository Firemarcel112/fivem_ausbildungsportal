<?php

namespace App\Http\Requests\Administration\Fractions;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('administration.fractions.edit');
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
                'max:255'
            ],
            'short_name' => [
                'required',
                'string',
                'max:5'
            ],
            'discord_webhook' => [
                'nullable',
                'string',
                'max:255'
            ],
            'discord_webhook_completed' => [
                'nullable',
                'string',
                'max:255'
            ],
            'master' => [
                'boolean'
            ],
        ];
    }
}
