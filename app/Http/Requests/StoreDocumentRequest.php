<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use App\Models\Qualification;
use App\Models\User\Account;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreDocumentRequest extends FormRequest
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
        $has_participant = Rule::requiredIf(!$this->filled('participant'));

        $account = new Account;
        $qualification = new Qualification;

        return [
            'trainer' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
            ],
            'participant' => [
                'nullable',
                'integer',
                Rule::exists($account->getTable(), $account->getKeyName()),
            ],
            'qualification_id' => [
                'required',
                Rule::exists($qualification->getTable(), $qualification->getKeyName()),
            ],
            'gender' => [
                $has_participant,
                'nullable',
                Rule::enum(Gender::class),
            ],
            'first_name' => [
                $has_participant,
                'nullable',
                'string',
                'max:255',
            ],
            'last_name' => [
                $has_participant,
                'nullable',
                'string',
                'max:255',
            ],
            'date_of_birth' => [
                $has_participant,
                'nullable',
                'date',
                Rule::date()->beforeToday(),
            ],
            'birth_location' => [
                $has_participant,
                'nullable',
                'string',
                'max:255',
            ],
            'training_date' => [
                $has_participant,
                'nullable',
                'date',
            ],
        ];
    }
}
