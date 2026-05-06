<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::beginTransaction();

        // First, let's create parent types for each group
        $parentTypes = [
            [
                'parent_id' => null,
                'name' => 'application_type',
                'urdu_name' => 'درخواست کی قسم',
                'description' => 'Certificate types like domicile/state subject'
            ],
            [
                'parent_id' => null,
                'name' => 'application_for',
                'urdu_name' => 'درخواست برائے',
                'description' => 'Who the application is for'
            ],
            [
                'parent_id' => null,
                'name' => 'application_nature',
                'urdu_name' => 'درخواست کی نوعیت',
                'description' => 'New/Duplicate applications'
            ],
            [
                'parent_id' => null,
                'name' => 'citizen_type',
                'urdu_name' => 'شہری کی قسم',
                'description' => 'Local/Refugee citizen types'
            ],
            [
                'parent_id' => null,
                'name' => 'delivery_option_type',
                'urdu_name' => 'ڈیلیوری آپشن',
                'description' => 'Urgent/Normal delivery options'
            ],

            [
                'parent_id' => null,
                'name' => 'guardian_type',
                'urdu_name' => 'سرپرست کی قسم',
                'description' => 'Guardian relationship types'
            ],
            [
                'parent_id' => null,
                'name' => 'religion',
                'urdu_name' => 'مذہب',
                'description' => 'Religions'
            ],
            [
                'parent_id' => null,
                'name' => 'gender',
                'urdu_name' => 'جنس',
                'description' => 'Gender types'
            ],
            [
                'parent_id' => null,
                'name' => 'marital_status',
                'urdu_name' => 'ازدواجی حیثیت',
                'description' => 'Marital status types'
            ],
            [
                'parent_id' => null,
                'name' => 'documentation_type',
                'urdu_name' => 'دستاویزات کی قسم',
                'description' => 'Document types required'
            ],
            [
                'parent_id' => null,
                'name' => 'govt_departments',
                'urdu_name' => 'سرکاری محکمے',
                'description' => 'Government departments'
            ],
            [
                'parent_id' => null,
                'name' => 'units',
                'urdu_name' => 'یونٹس',
                'description' => 'Measurement units'
            ],
            [
                'parent_id' => null,
                'name' => 'requisition_types',
                'urdu_name' => 'درخواست کی اقسام',
                'description' => 'Requisition types'
            ],
            [
                'parent_id' => null,
                'name' => 'financial_years',
                'urdu_name' => 'مالی سال',
                'description' => 'Financial years'
            ],
            [
                'parent_id' => null,
                'name' => 'client_type',
                'urdu_name' => 'کلائنٹ کی قسم',
                'description' => 'Client types'
            ],
            [
                'parent_id' => null,
                'name' => 'tax_types',
                'urdu_name' => 'ٹیکس کی اقسام',
                'description' => 'Tax types'
            ],
            [
                'parent_id' => null,
                'name' => 'payment_terms',
                'urdu_name' => 'ادائیگی کی شرائط',
                'description' => 'Payment terms'
            ],
            [
                'parent_id' => null,
                'name' => 'asset_class',
                'urdu_name' => 'جائیداد کی کلاس',
                'description' => 'Asset classes'
            ],
            [
                'parent_id' => null,
                'name' => 'asset_location',
                'urdu_name' => 'جائیداد کا مقام',
                'description' => 'Asset locations'
            ],
            [
                'parent_id' => null,
                'name' => 'departments',
                'urdu_name' => 'محکمے',
                'description' => 'Departments'
            ],
            [
                'parent_id' => null,
                'name' => 'vehicle_model',
                'urdu_name' => 'گاڑی ماڈل',
                'description' => 'Vehicle models'
            ],
            [
                'parent_id' => null,
                'name' => 'vehicle_type',
                'urdu_name' => 'گاڑی کی قسم',
                'description' => 'Vehicle types'
            ],
            [
                'parent_id' => null,
                'name' => 'vehicle_make',
                'urdu_name' => 'گاڑی ساز',
                'description' => 'Vehicle manufacturers'
            ],

            [
                'parent_id' => null,
                'name' => 'working_days',
                'urdu_name' => '',
                'description' => 'Working days'
            ],
            [
                'parent_id' => null,
                'name' => 'delivery_modes',
                'urdu_name' => 'ترسیل کے طریقے',
                'description' => 'Delivery modes'
            ],

        ];

        // Insert parent types and store their IDs
        $parentTypeIds = [];
        foreach ($parentTypes as $parentType) {
            $type = Type::create($parentType);
            $parentTypeIds[$parentType['name']] = $type->id;
        }

        // Now create child types under each parent
        $childTypes = [
            // Application Types (certificate types) => Corrected
            [
                'parent_id' => $parentTypeIds['application_type'],
                'name' => 'Domicile Certificate',
                'urdu_name' => 'رہائشی سرٹیفکیٹ',
                'description' => 'Residential certificate for local residents'
            ],
            [
                'parent_id' => $parentTypeIds['application_type'],
                'name' => 'State Subject Certificate',
                'urdu_name' => 'ریاستی موضوع کا سرٹیفکیٹ',
                'description' => 'Certificate for state subjects'
            ],
            [
                'parent_id' => $parentTypeIds['application_type'],
                'name' => 'Domicile/State Subject Certificate',
                'urdu_name' => 'ریاستی موضوع کا سرٹیفکیٹ/رہائشی سرٹیفکیٹ',
                'description' => 'Certificate for domicile and state subjects'
            ],

            // Application Nature => Corrected
            [
                'parent_id' => $parentTypeIds['application_nature'],
                'name' => 'New',
                'urdu_name' => 'نیا',
                'description' => 'New application'
            ],
            [
                'parent_id' => $parentTypeIds['application_nature'],
                'name' => 'Duplicate',
                'urdu_name' => 'نقل',
                'description' => 'Duplicate application'
            ],

            // Application For (who the application is for) => Corrected
            [
                'parent_id' => $parentTypeIds['application_for'],
                'name' => 'Self',
                'urdu_name' => 'خود',
                'description' => 'Application for self (alternate)'
            ],
            [
                'parent_id' => $parentTypeIds['application_for'],
                'name' => 'Father',
                'urdu_name' => 'والد',
                'description' => 'Application for father'
            ],
            [
                'parent_id' => $parentTypeIds['application_for'],
                'name' => 'Mother',
                'urdu_name' => 'والدہ',
                'description' => 'Application for mother'
            ],
            [
                'parent_id' => $parentTypeIds['application_for'],
                'name' => 'Husband',
                'urdu_name' => 'شوہر',
                'description' => 'Application for husband'
            ],
            [
                'parent_id' => $parentTypeIds['application_for'],
                'name' => 'Wife',
                'urdu_name' => 'بیوی',
                'description' => 'Application for wife'
            ],
            [
                'parent_id' => $parentTypeIds['application_for'],
                'name' => 'Other',
                'urdu_name' => 'دیگر',
                'description' => 'Application for other relative'
            ],

            // Citizen Type => Corrected
            [
                'parent_id' => $parentTypeIds['citizen_type'],
                'name' => 'Local',
                'urdu_name' => 'مقامی',
                'description' => 'Local citizen'
            ],
            [
                'parent_id' => $parentTypeIds['citizen_type'],
                'name' => 'Refugee',
                'urdu_name' => 'مہاجر',
                'description' => 'Refugee citizen'
            ],

            // Delivery Option => corrected
            [
                'parent_id' => $parentTypeIds['delivery_option_type'],
                'name' => 'Urgent',
                'urdu_name' => 'فوری',
                'description' => 'Urgent delivery'
            ],
            [
                'parent_id' => $parentTypeIds['delivery_option_type'],
                'name' => 'Normal',
                'urdu_name' => 'عام',
                'description' => 'Normal delivery'
            ],

            // Guardian Type => corrected
            [
                'parent_id' => $parentTypeIds['guardian_type'],
                'name' => 'Father',
                'urdu_name' => 'والد',
                'description' => 'Father as guardian'
            ],
            [
                'parent_id' => $parentTypeIds['guardian_type'],
                'name' => 'Husband',
                'urdu_name' => 'شوہر',
                'description' => 'Husband as guardian'
            ],
            [
                'parent_id' => $parentTypeIds['guardian_type'],
                'name' => 'Widow',
                'urdu_name' => 'بیوہ',
                'description' => 'Widow as guardian'
            ],

            // Religion => corrected
            [
                'parent_id' => $parentTypeIds['religion'],
                'name' => 'Islam',
                'urdu_name' => 'اسلام',
                'description' => 'Islamic religion'
            ],
            [
                'parent_id' => $parentTypeIds['religion'],
                'name' => 'Hinduism',
                'urdu_name' => 'ہندومت',
                'description' => 'Hindu religion'
            ],
            [
                'parent_id' => $parentTypeIds['religion'],
                'name' => 'Christianity',
                'urdu_name' => 'عیسائیت',
                'description' => 'Christian religion'
            ],
            [
                'parent_id' => $parentTypeIds['religion'],
                'name' => 'Ahmadiyya',
                'urdu_name' => 'احمدیایی',
                'description' => 'Ahmadiyya religion'
            ],
            [
                'parent_id' => $parentTypeIds['religion'],
                'name' => 'Sikhism',
                'urdu_name' => 'سکھ مت',
                'description' => 'Sikhism religion'
            ],

            // Gender => corrected
            [
                'parent_id' => $parentTypeIds['gender'],
                'name' => 'Male',
                'urdu_name' => 'مرد',
                'description' => 'Male gender'
            ],
            [
                'parent_id' => $parentTypeIds['gender'],
                'name' => 'Female',
                'urdu_name' => 'عورت',
                'description' => 'Female gender'
            ],
            [
                'parent_id' => $parentTypeIds['gender'],
                'name' => 'Other',
                'urdu_name' => 'دیگر',
                'description' => 'Other gender'
            ],

            // Marital Status => corrected
            [
                'parent_id' => $parentTypeIds['marital_status'],
                'name' => 'Married',
                'urdu_name' => 'شادی شدہ',
                'description' => 'Married'
            ],
            [
                'parent_id' => $parentTypeIds['marital_status'],
                'name' => 'Widowed (Female)',
                'urdu_name' => 'بیوہ',
                'description' => 'Widowed female'
            ],
            [
                'parent_id' => $parentTypeIds['marital_status'],
                'name' => 'Divorced',
                'urdu_name' => 'مطلقہ',
                'description' => 'Divorced'
            ],
            [
                'parent_id' => $parentTypeIds['marital_status'],
                'name' => 'Single',
                'urdu_name' => 'غیر شادی شدہ',
                'description' => 'Not married'
            ],
            [
                'parent_id' => $parentTypeIds['marital_status'],
                'name' => 'Separated',
                'urdu_name' => 'علیحدہ',
                'description' => 'Separated'
            ],
            [
                'parent_id' => $parentTypeIds['marital_status'],
                'name' => 'Widower (Male)',
                'urdu_name' => 'رنڈوا',
                'description' => 'Widowed male'
            ],
            [
                'parent_id' => $parentTypeIds['working_days'],
                'name' => 'Monday',
                'urdu_name' => 'پیر',
                'description' => 'Monday working day'
            ],
            [
                'parent_id' => $parentTypeIds['working_days'],
                'name' => 'Tuesday',
                'urdu_name' => 'منگل',
                'description' => 'Regular working day'
            ],
            [
                'parent_id' => $parentTypeIds['working_days'],
                'name' => 'Wednesday',
                'urdu_name' => 'بدھ',
                'description' => 'Midweek working day'
            ],
            [
                'parent_id' => $parentTypeIds['working_days'],
                'name' => 'Thursday',
                'urdu_name' => 'جمعرات',
                'description' => 'standard working day'
            ],
            [
                'parent_id' => $parentTypeIds['working_days'],
                'name' => 'Friday',
                'urdu_name' => 'جمعہ',
                'description' => 'Special day of the week'
            ],
            [
                'parent_id' => $parentTypeIds['working_days'],
                'name' => 'Saturday',
                'urdu_name' => 'ہفتہ',
                'description' => 'Half working day'
            ],
            [
                'parent_id' => $parentTypeIds['working_days'],
                'name' => 'Sunday',
                'urdu_name' => 'اتوار',
                'description' => 'Weekly holiday'
            ],

            //Delivery Modes
            [
                'parent_id' => $parentTypeIds['delivery_modes'],
                'name' => 'self',
                'urdu_name' => 'سیلف کلیکشن',
                'description' => 'Application for self pickup'
            ],
            [
                'parent_id' => $parentTypeIds['delivery_modes'],
                'name' => 'home',
                'urdu_name' => 'ہوم ڈیلیوری',
                'description' => 'Home delivery'
            ],
        ];

        // Insert all child types
        Type::insert($childTypes);

        DB::commit();
        Schema::enableForeignKeyConstraints();
    }
}
