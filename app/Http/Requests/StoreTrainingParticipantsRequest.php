<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Models\User\Account;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreTrainingParticipantsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()?->isTrainer();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $account_model = new Account;

        return [
            'account_ids' => [
                'required',
                'array',
            ],
            'account_ids.*' => [
                'required',
                'integer',
                Rule::exists($account_model->getTable(), $account_model->getKeyName()),
            ],
        ];
    }
}
