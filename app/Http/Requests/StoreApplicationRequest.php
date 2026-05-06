<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Base64Image;

class StoreApplicationRequest extends FormRequest
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
        $rules = [
            // Applicant
            'applicant.identity_number'          => 'required|max:15',
            'applicant.identity_type'            => ['required', 'in:local,refugee'],
            'applicant.full_name'                => 'required|string|max:100',
            'applicant.dob'                      => 'required|date',
            'applicant.pob'                      => 'nullable|string|max:100',
            'applicant.father_name'              => 'required|string|max:100',
            'applicant.father_identity_number'   => 'nullable|string|max:15',
            'applicant.phone'                    => 'required|string|max:15',
            'applicant.email'                    => 'nullable|email|max:100',
            'applicant.occupation'               => 'nullable|string|max:100',
            'applicant.wife_husband_name'        => 'nullable|string|max:100',
            'applicant.address'                  => 'required|string|max:255',
            'applicant.address2'                 => 'nullable|string|max:255',
            'applicant.address3'                 => 'nullable|string|max:255',
            'applicant.address4'                 => 'nullable|string|max:255',
            'applicant.tehsil_id'                => 'required|exists:demographies,id',
            'applicant.religion_id'              => 'required|exists:types,id',
            'applicant.gender_id'                => 'required|exists:types,id',
            'applicant.marital_status_id'        => 'required|exists:types,id',
            'applicant.guardian_type_id'         => 'nullable|exists:types,id',
            'applicant.state_subject_class'      => 'nullable|string|max:150',
            'applicant.identity_symbol'          => 'nullable|string|max:150',
            'applicant.residence_place'          => 'nullable|string|max:255',

            // Children
            'applicant.children'                 => 'nullable|array',
            'applicant.children.*.name'    => 'required|string|max:100',
            'applicant.children.*.age'           => 'required|integer|min:0|max:150',

            // Refugee Details - conditional on is_refugee
            'applicant.refugee_details'                        => 'required_if:applicant.identity_type,refugee|nullable',
            'applicant.refugee_details.refugee_from'           => 'required_if:applicant.identity_type,refugee|nullable|string|max:100',
            'applicant.refugee_details.refugee_year'           => 'required_if:applicant.identity_type,refugee|nullable|digits:4|integer',

            // Application
            'application.certificate_type'       => 'required|in:domicile,state,both',
            'application.application_type_id'    => 'required|exists:types,id',
            'application.application_for_id'     => 'required|exists:types,id',
            'application.center_id'              => 'nullable|exists:centers,id',
            'application.tehsil_id'              => 'required|exists:demographies,id',
            'application.district_id'            => 'required|exists:demographies,id',
            'application.region_id'              => 'nullable|exists:demographies,id',
            'application.guardian_type_id'       => 'nullable|exists:types,id',
            'application.missal_no'              => 'nullable|max:10',
            'application.entry_datetime'         => 'nullable|date',
            'application.amount'                 => 'nullable|numeric|min:0',
            'application.personal_image_file'    => ['required_without:application.personal_image', new Base64Image(2048, ['jpg', 'jpeg', 'png'])],
            'application.duplicate_details.reason' => 'required_if:application.application_type_id,2|max:1000',

            // Appointment
            'application.appointment.qmatic_token'      => 'nullable|string|max:20',
            'application.appointment.appointment_date'  => 'nullable|date',
            'application.appointment.appointment_time'  => 'nullable|date_format:H:i',
            'application.appointment.delivery_date'     => 'nullable|date|after:application.appointment.appointment_date',
        ];

        if($this->certificate_type==='state' || $this->certificate_type==='both'){
            $fingers = ['thumb', 'index', 'middle', 'ring', 'little'];

            $rules['application.biometrics'] = 'required|array';

            foreach ($fingers as $finger) {
                $base = "application.biometrics.$finger";

                $rules["$base.id"] = 'nullable|integer';
                $rules["$base.finger_type"] = "required|in:$finger";

                $rules["$base.applicant_id"] = 'nullable|exists:applicants,id';
                $rules["$base.application_id"] = 'nullable|exists:applications,id';

                $rules["$base.image_path"] = 'nullable|string';

                $rules["$base.image_file"] = [
                    "required_without:$base.image_path",
                    new Base64Image(2048, ['jpg', 'jpeg', 'png'])
                ];

                $rules["$base.feature_set"] = [
                    "required_without:$base.image_path"
                ];
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            // Applicant
            'applicant.identity_number.required' => 'شناختی نمبر لازمی ہے۔',
            'applicant.identity_number.max' => 'شناختی نمبر زیادہ سے زیادہ 15 حروف پر مشتمل ہو سکتا ہے۔',

            'applicant.identity_type.required' => 'شناخت کی قسم لازمی ہے۔',
            'applicant.identity_type.in' => 'شناخت کی منتخب کردہ قسم درست نہیں ہے۔',

            'applicant.full_name.required' => 'مکمل نام لازمی ہے۔',
            'applicant.full_name.max' => 'نام زیادہ سے زیادہ 100 حروف پر مشتمل ہو سکتا ہے۔',

            'applicant.dob.required' => 'تاریخ پیدائش لازمی ہے۔',
            'applicant.dob.date' => 'تاریخ پیدائش درست فارمیٹ میں نہیں ہے۔',

            'applicant.father_name.required' => 'والد کا نام لازمی ہے۔',

            'applicant.phone.required' => 'فون نمبر لازمی ہے۔',

            'applicant.email.email' => 'ای میل درست فارمیٹ میں نہیں ہے۔',

            'applicant.address.required' => 'پتہ لازمی ہے۔',

            'applicant.tehsil_id.required' => 'تحصیل کا انتخاب لازمی ہے۔',
            'applicant.tehsil_id.exists' => 'منتخب کردہ تحصیل درست نہیں ہے۔',

            'applicant.religion_id.required' => 'مذہب کا انتخاب لازمی ہے۔',
            'applicant.gender_id.required' => 'جنس کا انتخاب لازمی ہے۔',
            'applicant.marital_status_id.required' => 'ازدواجی حیثیت لازمی ہے۔',

            // Children
            'applicant.children.*.name.required' => 'بچے کا نام لازمی ہے۔',
            'applicant.children.*.age.required' => 'بچے کی عمر لازمی ہے۔',
            'applicant.children.*.age.integer' => 'بچے کی عمر عدد میں ہونی چاہیے۔',

            // Refugee
            'applicant.refugee_details.required_if' => 'مہاجر کی تفصیلات لازمی ہیں۔',
            'applicant.refugee_details.refugee_from.required_if' => 'مہاجر کہاں سے آیا یہ لازمی ہے۔',
            'applicant.refugee_details.refugee_year.required_if' => 'مہاجر کا سال لازمی ہے۔',

            // Application
            'application.certificate_type.required' => 'سرٹیفکیٹ کی قسم لازمی ہے۔',
            'application.certificate_type.in' => 'منتخب کردہ سرٹیفکیٹ کی قسم درست نہیں ہے۔',

            'application.application_type_id.required' => 'درخواست کی قسم لازمی ہے۔',
            'application.application_for_id.required' => 'درخواست کس کے لیے ہے یہ لازمی ہے۔',

            'application.tehsil_id.required' => 'تحصیل لازمی ہے۔',
            'application.district_id.required' => 'ضلع لازمی ہے۔',
            'application.missal_no.max' => 'Maximum 10 characters allowed for Missal No.',

            'application.personal_image_file.required_without' => 'تصویر لازمی ہے۔',

            //Duplicate Details
            'application.duplicate_details.reason.required_if' => 'براہ کرم وجہ درج کریں کیونکہ یہ ڈپلیکیٹ درخواست ہے۔',
            'application.duplicate_details.reason.max'         => 'وجہ زیادہ سے زیادہ 500 حروف پر مشتمل ہو سکتی ہے۔',

            // Appointment
            'application.appointment.qmatic_token.required' => 'ٹوکن نمبر لازمی ہے۔',

            'application.appointment.delivery_date.after' => 'ڈیلیوری کی تاریخ اپائنٹمنٹ کے بعد ہونی چاہیے۔',

            // Biometrics
            'application.biometrics.required' => 'بایومیٹرکس لازمی ہیں۔',

            'application.biometrics.*.finger_type.required' => 'فنگر کی قسم لازمی ہے۔',

            'application.biometrics.*.image_file.required_without' => 'فنگر کی تصویر لازمی ہے۔',
            'application.biometrics.*.feature_set.required_without' => 'فنگر کا ڈیٹا لازمی ہے۔',
        ];
    }

//    public function withValidator($validator)
//    {
//        $validator->after(function ($validator) {
//            $fingers = ['thumb', 'index', 'middle', 'ring', 'little'];
//
//            foreach ($fingers as $finger) {
//                $data = data_get($this->input(), "application.biometrics.$finger");
//
//                if (
//                    empty($data['image_file']) &&
//                    empty($data['image_path'])
//                ) {
//                    $validator->errors()->add(
//                        "application.biometrics.$finger.image_file",
//                        "Either image_file or image_path is required for $finger."
//                    );
//                }
//            }
//        });
//    }
}
