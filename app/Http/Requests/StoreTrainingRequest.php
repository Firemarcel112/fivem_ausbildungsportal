<?php

namespace App\Http\Requests;

use App\Models\Fraction;
use App\Models\Qualification;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreTrainingRequest extends FormRequest
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
        $trainer = new User;
        $fraction = new Fraction;

        return [
            'qualification_id' => [
                'required',
                'integer',
                Rule::exists($qualification->getTable(), $qualification->getKeyName()),
            ],
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

    /**
     * @return (callable(Validator ):void)[]
     */
    public function after()
    {
        return [
            function (Validator $validator) {
                if ($this->filled(['date', 'time'])) {
                    $time = Carbon::createFromFormat('Y-m-d H:i', $this->date . ' ' . $this->time);
                    $limit = (int) config('settings.training_creation_time_limit');
                    if ($time <= now()->addMinutes($limit)) {
                        $validator->errors()->add(
                            'date',
                            __('general.ausbildung_kann_nicht_in_vergangenheit_liegen'),
                        );
                    }
                }
            },
        ];
    }
}
