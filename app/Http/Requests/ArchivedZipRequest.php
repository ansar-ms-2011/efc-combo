<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ArchivedZipRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'zip_file' => 'nullable|mimes:zip|max:102400', // 1MB
        ];
    }
    public function messages(): array
    {
        return [
            'zip_file.required' => 'A ZIP file is required.',
            'zip_file.mimes' => 'The file must be a ZIP .',
            'zip_file.max' => 'The ZIP file may not be greater than 1MB.',
        ];
    }
}
