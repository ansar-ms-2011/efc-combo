<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArchiveApplicantRequest extends FormRequest
{
    public function authorize(): bool { return true; }

  public function rules(): array
{
    $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

    return [
        'name'            => 'nullable|string|max:255',
        'father_name'     => 'nullable|string|max:255', 
        'identity_type'   => 'nullable|in:domicile,state',
         'is_refugee'     => 'boolean',
        'cnic'           => (filter_var($this->is_refugee, FILTER_VALIDATE_BOOLEAN)) 
                        ?  'nullable|string' : 'nullable|string|regex:/^[0-9]{5}-[0-9]{7}-[0-9]{1}$/', 'tehsil_id'       => 'nullable|exists:demographies,id',     
        'refugee_number'  => 'nullable|string|max:50',
        'misal_no'        => 'nullable|string|max:100',
        
        
        'documents'       => ($isUpdate ? 'nullable' : 'required_without:zip_file') . '|array',   
        'documents.*.file_path' => 'required|string',
        'documents.*.file_name' => 'required|string',
        'zip_file' => 'nullable|mimes:zip|max:102400',
    ];
}

    public function messages(): array
    {
        return [
        'documents.required' => 'Document is required.',

    ];
    }
} 