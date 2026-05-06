<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RequiredDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add error handling for missing Type records
        $lostType = Type::where('name', 'Lost/Stolen')->first();
        $fadedOutType = Type::where('name', 'Faded out')->first();
        $afterMarriageType = Type::where('name', 'Name Change After Marriage')->first();

        // Check if required types exist
        if (!$lostType || !$fadedOutType || !$afterMarriageType) {
            $this->command->error('Required Type records not found!');
            $this->command->info('Make sure you run TypeSeeder first.');
            return;
        }

        $documents = [
            ['key' => 'affidavit', 'name' => 'Affidavit', 'urdu_name' => 'حلف نامہ', 'required_copy' => 'original', 'service_name' => 'both', 'service_type' => 'new', 'reason_type_id' => null, 'active' => '1'],
            ['key' => 'application_form', 'name' => 'Application Form', 'urdu_name' => 'درخواست فارم', 'required_copy' => 'original', 'service_name' => 'both', 'service_type' => 'duplicate', 'reason_type_id' => null, 'active' => '1'],
            ['key' => 'bank_chalan_form', 'name' => 'Bank Chalan Form', 'urdu_name' => 'بینک چالان فارم', 'required_copy' => 'original', 'service_name' => 'both', 'service_type' => 'both', 'reason_type_id' => null, 'active' => '1'],
            ['key' => 'cnic_related', 'name' => 'CNIC / Birth Certificate', 'urdu_name' => 'شناختی کارڈ / پیدائش کا سرٹیفکیٹ', 'required_copy' => 'original', 'service_name' => 'both', 'service_type' => 'both', 'reason_type_id' => null, 'active' => '1'],

            ['key' => 'fir_copy', 'name' => 'FIR', 'urdu_name' => 'فرسٹ انفارمیشن رپورٹ کی کاپی', 'required_copy' => 'original', 'service_name' => 'both', 'service_type' => 'duplicate', 'reason_type_id' => $lostType->id, 'active' => '1'],
            ['key' => 'original_domicile', 'name' => 'Original Domicile', 'urdu_name' => 'اصل رہائشی ڈومیسائل', 'required_copy' => 'original', 'service_name' => 'both', 'service_type' => 'duplicate', 'reason_type_id' => $fadedOutType->id, 'active' => '1'],
            ['key' => 'nikah_nama', 'name' => 'Nikah Nama', 'urdu_name' => 'نکاح نامہ', 'required_copy' => 'original', 'service_name' => 'both', 'service_type' => 'duplicate', 'reason_type_id' => $afterMarriageType->id, 'active' => '1'],

            ['key' => 'cnic_back_domicile', 'name' => 'CNIC Back Domicile', 'urdu_name' => 'شناختی کارڈ کی پشت / ڈومیسائل', 'required_copy' => 'original', 'service_name' => 'both', 'service_type' => 'both', 'reason_type_id' => null, 'active' => '0'],
            ['key' => 'state_subject_copy', 'name' => 'Copy of State Subject', 'urdu_name' => 'اسٹیٹ سبجیکٹ کی کاپی', 'required_copy' => 'original', 'service_name' => 'both', 'service_type' => 'both', 'reason_type_id' => null, 'active' => '0'],
            ['key' => 'decision_form_domicile', 'name' => 'Decision Form Domicile', 'urdu_name' => 'ڈومیسائل فیصلہ فارم', 'required_copy' => 'original', 'service_name' => 'both', 'service_type' => 'both', 'reason_type_id' => null, 'active' => '0'],
            ['key' => 'family_tree', 'name' => 'Family Tree', 'urdu_name' => 'خاندانی شجرہ', 'required_copy' => 'original', 'service_name' => 'both', 'service_type' => 'duplicate', 'reason_type_id' => null, 'active' => '0'],
            ['key' => 'form_5', 'name' => 'Form 5', 'urdu_name' => 'فارم 5', 'required_copy' => 'original', 'service_name' => 'both', 'service_type' => 'both', 'reason_type_id' => null, 'active' => '0'],
            ['key' => 'passport_photograph', 'name' => 'Fresh Photograph (Passport Size)', 'urdu_name' => 'نئی تصویر (پاسپورٹ سائز)', 'required_copy' => 'original', 'service_name' => 'both', 'service_type' => 'both', 'reason_type_id' => null, 'active' => '0'],
            ['key' => 'girdawri', 'name' => 'Girdawri', 'urdu_name' => 'گرداوری', 'required_copy' => 'original', 'service_name' => 'both', 'service_type' => 'both', 'reason_type_id' => null, 'active' => '0'],
            ['key' => 'marriage_certificate', 'name' => 'Marriage Certificate', 'urdu_name' => 'نکاح نامہ / شادی کا سرٹیفکیٹ', 'required_copy' => 'original', 'service_name' => 'both', 'service_type' => 'both', 'reason_type_id' => null, 'active' => '0'],
            ['key' => 'news_report', 'name' => 'News Report', 'urdu_name' => 'خبری رپورٹ', 'required_copy' => 'original', 'service_name' => 'both', 'service_type' => 'both', 'reason_type_id' => null, 'active' => '0'],
            ['key' => 'noc_domicile', 'name' => 'NOC for Domicile', 'urdu_name' => 'ڈومیسائل کے لیے این او سی', 'required_copy' => 'original', 'service_name' => 'both', 'service_type' => 'both', 'reason_type_id' => null, 'active' => '0'],
            ['key' => 'police_report', 'name' => 'Police Report', 'urdu_name' => 'پولیس رپورٹ', 'required_copy' => 'original', 'service_name' => 'both', 'service_type' => 'both', 'reason_type_id' => null, 'active' => '0'],
            ['key' => 'property_documents', 'name' => 'Property Documents / Utility Bills / Resident Certificate / Education Document / Voter List', 'urdu_name' => 'جائیداد کے کاغذات / یوٹیلٹی بلز / رہائشی سرٹیفکیٹ / تعلیمی دستاویز / ووٹر لسٹ', 'required_copy' => 'original', 'service_name' => 'both', 'service_type' => 'both', 'reason_type_id' => null, 'active' => '0'],
            ['key' => 'report', 'name' => 'Report', 'urdu_name' => 'رپورٹ', 'required_copy' => 'original', 'service_name' => 'both', 'service_type' => 'both', 'reason_type_id' => null, 'active' => '0'],
            ['key' => 'patwari_report', 'name' => 'Report Patwari', 'urdu_name' => 'پٹواری رپورٹ', 'required_copy' => 'original', 'service_name' => 'both', 'service_type' => 'both', 'reason_type_id' => null, 'active' => '0'],
            ['key' => 'signed_certificate_copy', 'name' => 'Signed Copy of Certificate', 'urdu_name' => 'سرٹیفکیٹ کی دستخط شدہ کاپی', 'required_copy' => 'original', 'service_name' => 'both', 'service_type' => 'both', 'reason_type_id' => null, 'active' => '0'],
            ['key' => 'sokhta_girdawri_report', 'name' => 'Sokhta Girdawri Report', 'urdu_name' => 'سوختہ گرداوری رپورٹ', 'required_copy' => 'original', 'service_name' => 'both', 'service_type' => 'both', 'reason_type_id' => null, 'active' => '0'],
            ['key' => 'statement_form', 'name' => 'Statement Form', 'urdu_name' => 'بیان فارم', 'required_copy' => 'original', 'service_name' => 'both', 'service_type' => 'both', 'reason_type_id' => null, 'active' => '0'],
            ['key' => 'witness_form_domicile', 'name' => 'Witness Form Domicile', 'urdu_name' => 'گواہ فارم (ڈومیسائل)', 'required_copy' => 'original', 'service_name' => 'both', 'service_type' => 'both', 'reason_type_id' => null, 'active' => '0'],
        ];

        foreach ($documents as $document) {
            DB::table('required_documents')->insert([
                ...$document,
                'max_size_in_mb' => 1,
                'max_size_in_bytes' => 1024 * 1024,
            ]);
        }
    }
}
