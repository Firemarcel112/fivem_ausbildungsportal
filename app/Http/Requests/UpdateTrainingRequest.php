<?php

namespace App\Http\Requests;

use App\Models\Fraction;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTrainingRequest extends FormRequest
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
        $trainer = new User;
        $fraction = new Fraction;

        return [
            'trainer_id' => [
                'required',
                'integer',
                Rule::exists($trainer->getTable(), $trainer->getKeyName()),
            ],
            'meeting_point' => [
                'required',
                'string',
                'max:255',
            ],
            'date' => [
                'required',
                'date',
            ],
            'time' => [
                'required',
                'date_format:H:i',
            ],
            'min_participants' => [
                'required',
            ],
            'max_participants' => [
                'required',
                'gte:min_participants',
            ],
            'additional_information' => [
                'nullable',
                'string',
            ],
            'discord_notification' => [
                'nullable',
            ],
            'discord_notification.*' => [
                function ($attribute, $value, $fail) use ($fraction) {
                    if ($value == 'alle') {
                        return;
                    }

                    $exists = \DB::table($fraction->getTable())
                        ->where($fraction->getKeyName(), $value)
                        ->exists();

                    if (!$exists) {
                        $fail(__('validation.exists', ['attribute' => $attribute]));
                    }
                },
            ],
        ];
    }
}
