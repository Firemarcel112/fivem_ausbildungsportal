<?php

namespace App\Http\Requests;

use App\Models\Qualification;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreTrainingRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $qualification = new Qualification;

        return [
            'qualification_id' => [
                'required',
                Rule::exists($qualification->getTable(), $qualification->getKEyName()),
            ],
            'date' => [
                'required',
                'date',
                Rule::date()->afterToday(),
            ],
            'time' => [
                'required',
                'date_format:H:i',
            ],
        ];
    }
}
