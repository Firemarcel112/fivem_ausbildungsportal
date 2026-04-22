<?php

namespace App\Http\Requests;

use App\Models\Trainings\Participant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CompletedTrainingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()?->isTrainer() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $participant_model = new Participant;

        return [
            'cancelled' => [
                'nullable',
                'boolean',
            ],
            'cancelled_notice' => [
                'nullable',
                'string',
            ],
            'participants' => [
                Rule::requiredIf(!$this->cancelled),
                'array',
            ],

            'participants.*.present' => [
                'boolean',
            ],
            'participants.*.passed' => [
                'boolean',
            ],
            'participants.*.signed_out' => [
                'boolean',
            ],
        ];
    }
}
