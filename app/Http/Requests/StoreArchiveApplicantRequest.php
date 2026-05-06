<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreArchiveApplicantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'is_refugee' => 'required|boolean',
            'cnic' => [
                'nullable',
                'required_if:is_refugee,false',
                'regex:/^\d{5}-\d{7}-\d{1}$/',
                Rule::unique('archive_applicants')->ignore($this->route('archive_applicant')),
            ],
            'refugee_number' => 'required_if:is_refugee,true|nullable|string|max:255',
            'from' => 'nullable|string|max:10',
            'to' => 'nullable|string|max:10',
             'identity_type' => 'required|string',
            'documents' => 'nullable|array',
            'documents.*.certificate_number' => 'required|string',
            'documents.*.certificate_type' => 'required|string|in:pdf,image',
            'documents.*.file_name' => 'required|string',
            'documents.*.file_path' => 'required|string',
            'documents.*.file_type' => 'required|string|in:PDF,JPG,PNG,TIFF',
            'documents.*.file_size' => 'required|integer',
            'documents.*.status' => 'nullable|string|in:pending,rejected,verified,corrected',
            'tehsil' => 'required|string|max:255',
            'misal_no' => 'required|string|max:100',

        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required',
            'cnic.required_if' => 'CNIC is required for non-refugee applicants',
            'cnic.regex' => 'CNIC must be in format 12345-1234567-1',
            'cnic.unique' => 'CNIC already exists',
            'refugee_number.required_if' => 'Refugee number is required for refugee applicants',
            'is_refugee.required' => 'Please specify if applicant is a refugee',
            'tehsil.required' => 'Tehsil is required',
            'misal_no.required' => 'Misal number is required',
        ];
    }
}