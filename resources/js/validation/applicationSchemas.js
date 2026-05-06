// import * as yup from 'yup';
import yup from './yupExtension.js';

export const validationSchema = yup.object({
    applicant: yup.object({
        identity_number: yup
            .string()
            .max(15, 'زیادہ سے زیادہ 15 حروف کی اجازت ہے')
            .required('شناختی نمبر لازمی ہے'),
        identity_type: yup
            .string()
            .max(15, 'زیادہ سے زیادہ 15 حروف کی اجازت ہے')
            .required('شناخت کی قسم لازمی ہے'),
        pob: yup
            .string()
            .max(50, 'زیادہ سے زیادہ 50 حروف کی اجازت ہے')
            .nullable(),
        dob: yup.date()
            .transform((value, originalValue) => {
                return originalValue === '' ? null : value;
            })
            .nullable()
            .required('تاریخ پیدائش لازمی ہے')
            .test(
                'not-today-or-future',
                'تاریخ آج یا مستقبل کی نہیں ہو سکتی',
                (value) => {
                    if (!value) return true;

                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    const inputDate = new Date(value);
                    inputDate.setHours(0, 0, 0, 0);

                    // ✅ must be strictly less than today
                    return inputDate.getTime() < today.getTime();
                }
            ),
        identity_symbol: yup
            .string()
            .max(50, 'زیادہ سے زیادہ 50 حروف کی اجازت ہے')
            .required('یہ فیلڈ لازمی ہے'),
        full_name: yup
            .string()
            .max(50, 'زیادہ سے زیادہ 50 حروف کی اجازت ہے')
            .required('یہ فیلڈ لازمی ہے'),
        email: yup
            .string()
            .max(50, 'زیادہ سے زیادہ 50 حروف کی اجازت ہے')
            .email('براہِ کرم درست ای میل درج کریں')
            .nullable(),
        guardian_type_id: yup
            .number()
            .transform((value, originalValue) => {
                return originalValue === '' ? null : value;
            })
            .nullable()
            .required('یہ فیلڈ لازمی ہے'),
        father_identity_number: yup
            .string()
            .matches(/^\d{5}-\d{7}-\d{1}$/, 'درست شناختی کارڈ نمبر درج کریں')
            .max(15, 'زیادہ سے زیادہ 15 حروف کی اجازت ہے')
            .nullable(),
        father_name: yup
            .string()
            .max(50, 'زیادہ سے زیادہ 50 حروف کی اجازت ہے')
            .required('یہ فیلڈ لازمی ہے'),
        gender_id: yup
            .number()
            .transform((value, originalValue) => {
                return originalValue === '' ? null : value;
            })
            .nullable()
            .required('یہ فیلڈ لازمی ہے'),
        religion_id: yup
            .number()
            .transform((value, originalValue) => {
                return originalValue === '' ? null : value;
            })
            .nullable()
            .required('یہ فیلڈ لازمی ہے'),
        phone: yup
            .string()
            .required('موبائل نمبر لازمی ہے')
            .matches(/^03\d{2}-\d{7}$/, 'درست موبائل نمبر درج کریں'),
        occupation: yup
            .string()
            .nullable()
            .max(255, 'زیادہ سے زیادہ 255 حروف کی اجازت ہے'),
        state_subject_class: yup
            .string()
            .nullable()
            .max(100, 'زیادہ سے زیادہ 100 حروف کی اجازت ہے')
            .when('$application.certificate_type', ([certificateType], schema) => {
                return (certificateType === 'state' || certificateType === 'both') ? schema.required('اسٹیٹ سبجیکٹ کے لیے کلاس لازمی ہے') : schema.notRequired();
            }),
        residence_place: yup
            .string()
            .nullable()
            .max(150, 'زیادہ سے زیادہ 150 حروف کی اجازت ہے'),
        marital_status_id: yup
            .number()
            .transform((value, originalValue) => {
                return originalValue === '' ? null : value;
            })
            .nullable()
            .required('یہ فیلڈ لازمی ہے'),
        children: yup.array().of(
            yup.object({
                name: yup
                    .string()
                    .required('بچے کا نام لازمی ہے')
                    .matches(/^[^\d]+$/, 'بچے کے نام میں نمبرز نہیں ہو سکتے')
                    .matches(/^[a-zA-Z\u0600-\u06FF\s]+$/, 'صرف حروف اور خالی جگہ کی اجازت ہے'),
                age: yup
                    .number()
                    .transform((value, originalValue) => {
                        return originalValue === '' ? null : value;
                    })
                    .nullable()
                    .required('بچے کی عمر لازمی ہے')
                    .min(0, 'عمر صفر یا اس سے زیادہ ہونی چاہیے')
                    .max(100, 'عمر 100 سے زیادہ نہیں ہو سکتی')
                    .integer('عمر مکمل عدد ہونی چاہیے')
            })
        ),
        refugee_details: yup.object({
            refugee_from: yup.string().when('$applicant.identity_type', ([identityType], schema) => {
                return identityType === 'refugee' ? schema.required('مہاجر کے لیے یہ فیلڈ ضروری ہے') : schema.notRequired();
            }),
            refugee_year: yup.number()
                .transform((value, originalValue) => {
                    return originalValue === '' ? null : value; // ✅ fix
                })
                .nullable() // ✅ allow null
                .typeError('سال ایک نمبر ہونا چاہیے')
                .integer('سال مکمل عدد ہونا چاہیے')
                .min(1901, 'سال کم از کم 1901 ہونا چاہیے')
                .max(2155, 'سال زیادہ سے زیادہ 2155 ہونا چاہیے')
                .when('$applicant.identity_type', ([identityType], schema) => {
                    return identityType === 'refugee' ? schema.required('مہاجر کے لیے یہ فیلڈ ضروری ہے') : schema.notRequired();
                })
        }),
        region_id: yup
            .number()
            .transform((value, originalValue) => {
                return originalValue === '' ? null : value;
            })
            .nullable()
            .required('علاقے کا نام لازمی ہے'),
        district_id: yup
            .number()
            .transform((value, originalValue) => {
                return originalValue === '' ? null : value;
            })
            .nullable()
            .required('ضلع کا نام لازمی ہے'),
        tehsil_id: yup
            .number()
            .transform((value, originalValue) => {
                return originalValue === '' ? null : value;
            })
            .nullable()
            .required('تحصیل کا نام لازمی ہے'),
        location: yup
            .string()
            .required('یہ فیلڈ لازمی ہے').max(200, 'زیادہ سے زیادہ 200 حروف کی اجازت ہے'),
        address: yup
            .string()
            .required('یہ فیلڈ لازمی ہے۔')
            .max(500, 'زیادہ سے زیادہ 500 حروف کی اجازت ہے'),
        address2: yup
            .string()
            .when(['$applicant.identity_type', '$application.certificate_type'], ([identityType, certificateType], schema) => {
                return identityType === 'refugee' && (certificateType === 'state' || certificateType === 'both') ? schema.required('مہاجر کے لیے یہ فیلڈ ضروری ہے').max(500, 'زیادہ سے زیادہ 500 حروف کی اجازت ہے') : schema.notRequired();
            }),
        address3: yup
            .string()
            .when(['$applicant.identity_type', '$application.certificate_type'], ([identityType, certificateType], schema) => {
                return identityType === 'refugee' && (certificateType === 'state' || certificateType === 'both') ? schema.required('یہ فیلڈ ضروری ہے').max(500, 'زیادہ سے زیادہ 500 حروف کی اجازت ہے') : schema.notRequired();
            })
    }),
    application: yup.object({
        application_type_id: yup
            .number()
            .required('درخواست کی قسم لازمی ہے'),
        certificate_type: yup
            .string()
            .required('سرٹیفکیٹ کی قسم لازمی ہے'),
        entry_datetime: yup.date()
            .transform((value, originalValue) => {
                return originalValue === '' ? null : value;
            })
            .nullable()
            .required('اندراج کی تاریخ لازمی ہے')
            .test(
                'not-today',
                'تاریخ مستقبل کی نہیں ہو سکتی',
                (value) => {
                    if (!value) return true; // skip if empty (required handles empty)
                    const today = new Date();
                    today.setHours(0, 0, 0, 0); // normalize to midnight
                    const inputDate = new Date(value);
                    inputDate.setHours(0, 0, 0, 0);
                    return inputDate.getTime() <= today.getTime();
                }
            ),
        application_for_id: yup
            .number()
            .transform((value, originalValue) => {
                return originalValue === '' ? null : value;
            })
            .nullable()
            .required('یہ فیلڈ لازمی ہے'),
        missal_no: yup
            .string()
            .nullable()
            .transform((value) => value === '' ? null : value)
            .matches(/^\d+$/, 'صرف نمبرز درج کریں'),
        appointment: yup.object({
            qmatic_token: yup.string().nullable()
        }),
        personal_image_file: yup
            .mixed()
            .when(['$application.personal_image'], ([personalImage], schema) => {
                return (personalImage === null || personalImage === '') ? schema.required('درخواست دہندہ کی تصویر لازمی ہے۔') : schema.notRequired();
            }).validateImage({
                maxFileSizeMB: 1,
                maxLength: 512,
                maxHeight: 512
            }),
        biometrics: yup.object().when(['$application.certificate_type', '$application.application_type_id'], ([certificateType, applicationTypeId], schema) => {
            if ((certificateType === 'state' || certificateType === 'both') && applicationTypeId === 1) {
                return schema.shape({
                    thumb: fingerValidation('Thumb'),
                    index: fingerValidation('Index'),
                    middle: fingerValidation('Middle'),
                    ring: fingerValidation('Ring'),
                    little: fingerValidation('Little')
                });
            }
            return schema.notRequired();
        }),
        documents: yup.array().of(
            yup.object({
                id: yup.mixed().nullable(),
                key: yup.mixed().nullable(),
                file_path: yup.mixed().nullable(),
                application_id: yup.mixed().nullable(),
                max_size_in_mb: yup.number().nullable(), // Add this field to the document object
                new_file: yup.string().nullable().test({
                    name: 'fileSize',
                    test: function(value) {
                        if (!value) return true;

                        const maxSize = this.parent?.max_size_in_mb;

                        const limit = maxSize ? maxSize * 1024 * 1024 : 2 * 1024 * 1024;

                        const base64 = value.split(',')[1];
                        if (!base64) return true;

                        const padding = (base64.match(/=+$/) || [''])[0].length;
                        const size = (base64.length * 3) / 4 - padding;
                        const isValid = size <= limit;

                        if (!isValid) {
                            const maxSizeMB = maxSize ? maxSize : 2;
                            return this.createError({
                                message: `فائل کا سائز ${maxSizeMB} ایم بی سے زیادہ نہیں ہونا چاہیے۔`
                            });
                        }
                        return true;
                    }
                })
            })
        ).nullable(),
        // .when('file_path', {
        //     is: (filePath) => !filePath || filePath === '',
        //     then: (schema) => schema.required('فائل اپلوڈ کرنا لازمی ہے۔'),
        //     otherwise: (schema) => schema.notRequired()
        // })
        delivery_details: yup.object({
            delivery_mode: yup.string().required('ترسیل کا کوئی ایک طریقہ منتخب کریں'),
            delivery_address: yup.string().when('$application.delivery_details.delivery_mode', ([deliveryMode], schema) => {
                return deliveryMode === 'home' ? schema.required('ہوم ڈیلیوری کے لیے مکمل پتہ درج کریں').max(255, 'زیادہ سے زیادہ 255 حروف کی اجازت ہے') : schema.notRequired();
            })
        }),
        duplicate_details: yup.object({
            application_id: yup.mixed().nullable(),
            reason_type_id: yup.number().transform((value, originalValue) => {
                return originalValue === '' ? null : value;
            }).when(['$application.application_type_id'], ([applicationTypeId], schema) => {
                return applicationTypeId === 2 ? schema
                    .required('یہ فیلڈ ضروری ہے') : schema.notRequired();
            }),
            reason: yup
                .string()
                .when(['$application.application_type_id'], ([applicationTypeId], schema) => {
                    return applicationTypeId === 2 ? schema
                        .required('یہ فیلڈ ضروری ہے')
                        .max(500, 'زیادہ سے زیادہ 1000 حروف کی اجازت ہے') : schema.notRequired();
                })
        })
    })
});

const fingerValidation = (label) =>
    yup.object({
        image_file: yup.mixed().nullable(),
        image_path: yup.string().nullable()
    }).test(
        `${label}-required`,
        ` انگلی کے فنگر پرنٹ ضروری ہے `,
        value => value?.image_file || value?.image_path
    );

export const step1FieldNames = [
    // Application fields
    'application.certificate_type',
    'application.application_type_id',
    'application.application_for_id',
    'application.missal_no',
    'application.appointment.qmatic_token',
    'application.entry_datetime',
    'application.duplicate_details.reason_type_id',
    'application.duplicate_details.reason',

    // Applicant fields
    'applicant.identity_number',
    'applicant.identity_type',
    'applicant.refugee_details.refugee_from',
    'applicant.refugee_details.refugee_year',
    'applicant.pob',
    'applicant.dob',
    'applicant.full_name',
    'applicant.email',
    'applicant.guardian_type_id',
    'applicant.father_identity_number',
    'applicant.father_name',
    'applicant.gender_id',
    'applicant.religion_id',
    'applicant.phone',
    'applicant.marital_status_id',
    'applicant.state_subject_class',
    'applicant.residence_place',
    'applicant.occupation',
    'applicant.remarks',
    'applicant.wife_husband_name',

    // Children fields (dynamic array)
    'applicant.children',
    'applicant.children[${index}].id',
    'applicant.children[${index}].applicant_id',
    'applicant.children[${index}].application_id',
    'applicant.children[${index}].age',
    'applicant.children[${index}].name'
];

export const step2FieldNames = [
    // Application fields
    'applicant.region_id',
    'applicant.district_id',
    'applicant.tehsil_id',
    'applicant.location',
    'applicant.address',
    'applicant.address2',

    //Delivery Details
    'application.delivery_details.delivery_mode',
    'application.delivery_details.delivery_address',
    'application.delivery_details.delivery_phone'
];

export const step3FieldNames = [
    'application.personal_image_file',
    'application.biometrics.thumb',
    'application.biometrics.index',
    'application.biometrics.middle',
    'application.biometrics.ring',
    'application.biometrics.little'

    // Documents fields (dynamic array)
];
