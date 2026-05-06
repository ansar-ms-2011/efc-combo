<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ForwardApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return in_array(auth()->user()->current_role?->name, ['DEO', 'AC', 'ACR', 'DC','Center In-charge']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'action' => ['required', 'string', 'in:forward,objected'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ];

        if ($this->action === 'objected') {
            $rules['remarks'] = ['required', 'string', 'max:1000'];
        }

        if (in_array($this->action, ['forward', 'verified', 'approved'])) {
            $rules['name'] = ['nullable', 'string', 'max:100'];
            $rules['designation'] = ['nullable', 'string', 'max:100'];
            $rules['signature'] = ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'action.required' => 'Choose an action.',
            'remarks.required' => 'Brief remarks are required for objecting.',
        ];
    }
}
