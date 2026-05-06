<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserFormRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->route('user')),
            ],
            'password' => [
                'nullable',
                'confirmed',
                Password::min(8)->letters()->numbers(),
            ],
            'center_id' => 'nullable|integer|exists:centers,id',
            'role_id' => 'required|integer|exists:roles,id',
            'cnic' => ['nullable', 'regex:/^[0-9]{5}-[0-9]{7}-[0-9]{1}$/'],
            'phone_no' => 'nullable|string|max:9',
            'address' => 'nullable|string|max:35',
            'prefix' => 'nullable|string|max:10',
        ];
        if ($this->role_name === 'Commissioner') {
            $rules['region_id'] = 'required|integer|exists:demographies,id';
        }
        if ($this->role_name === 'DC' || $this->role_name === 'AC' || $this->role_name === 'ACR' || $this->role_name === 'DEO' || $this->role_name === 'Center In-charge'|| $this->role_name === 'Patwari') {
            $rules['district_id'] = 'required|integer|exists:demographies,id';
        }
        if ($this->role_name === 'Patwari'){
            $rules['city_id'] = 'required|integer|exists:demographies,id';
        }
        if ($this->role_name === 'AC' || $this->role_name === 'ACR' || $this->role_name === 'DEO' || $this->role_name === 'Center In-charge' || $this->role_name === 'Patwari') {
             $rules['tehsil_id'] = 'required|integer|exists:demographies,id';
            $rules['tehsil_id'] = 'required|integer|exists:demographies,id';
        }
        if ($this->role_name === 'DEO' || $this->role_name === 'Center In-charge') {
            $rules['center_id'] = 'required|integer|exists:centers,id';
        }
        if ($this->role_name === 'AC' || $this->role_name === 'ACR' || $this->role_name === 'DC') {
            $rules['sign_file'] = [
                'required_without:sign_url',
                'file',
                'mimes:png',
                'max:3072',
            ];
        }
        return $rules;
    }

    public function messages(): array
    {
        return [
            'cnic.required' => 'CNIC field is required.',
            'cnic.regex' => 'Invalid CNIC format.',
            'tehsil_id.required' => 'Tehsil field is required.',
            'district_id.required' => 'District field is required.',
            'center_id.required' => 'Center field is required.',
            'sign_file.required_without' => 'Sign file is required for roles [DC, AC, ACR].',
        ];
    }

    public function authorize(): bool
    {
        return Auth::user()->hasRole(['Super Admin', 'Center In-charge']);
    }
}
