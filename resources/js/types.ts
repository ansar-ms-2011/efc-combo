export interface Demography {
    id: number;
    name: string;
    urdu_name?: string;

    [key: string]: any; // optional extra properties
}

export interface Center {
    id: number;
    name: string;

    [key: string]: any;
}

export interface Role {
    id: number;
    name: string;
}

export interface User {
    id: number;
    name: string;
    first_name: string;
    last_name: string;
    last_activity: string;
    username: string;
    email: string;
    roles: Role[];
    role: string;
    permissions: string[];
    region_id: number;
    district_id: number;
    tehsil_id: number;
    center_id: number;
    status: string;
    created_at: string;
    updated_at: string;
    allowed_services: string[];
    keyboard_settings?: {
        urduInput?: boolean;
    } | null;
}

export interface Region {
    id: number;
    name: string;
    urdu_name?: string;
    parent_id?: number;
    districts?: District[];
}

export interface District {
    id: number;
    name: string;
    urdu_name?: string;
    parent_id?: number;
    tehsils?: Tehsil[];
}

export interface Tehsil {
    id: number;
    name: string;
    urdu_name?: string;
    parent_id?: number;
}

export interface Type {
    id: number;
    name: string;
    urdu_name?: string;
    created_at: string;
    updated_at: string;
    parent_id?: number;
}

export interface Backup {
    id: number;
    type: 'monthly' | 'yearly' | 'manual';
    scope: 'monthly' | 'yearly' | 'full_site';
    status: 'pending' | 'processing' | 'completed' | 'failed';
    file_path: string | null;
    progress_percentage: number;
    created_at: string;
    completed_at?: string | null;
    error_message?: string | null;
}

export interface Certificate {
    id: number;
    applicant_id: number;
    application_id: number;
    type: 'state' | 'domicile';
    certificate_number: string;
    issue_date: string;
    status: 'pending' | 'ready' | 'error';
    uploaded_by: number;
    source: string;
    is_revoked: boolean;
    pdf_path: string | null;
    created_at: string;
    updated_at: string;
}

export interface Applicant {
    id: string;
    uuid: string;
    full_name: string;
    identity_number: string;
    identity_type: string;
    dob: string;
    pob: string;
    identity_symbol: string;
    father_name: string;
    father_identity_number: string;
    email: string;
    phone: string;
    occupation: string;
    wife_husband_name: string;
    guardian_type_id: number;
    state_subject_class: string;
    residence_place: string;
    address: string;
    address2: string;
    address3: string;
    address4: string;
    region_id:  number;
    district_id:  number;
    tehsil_id:  number;
    religion_id:  number;
    gender_id:  number;
    marital_status_id:  number;
    citizen_type_id:  number;
    location: string;
    personal_image: string;
    status: string;
    created_by: string;
    updated_by: string;
    deleted_by: string | null;  // Nullable if soft delete is used
    deleted_at: string | null;   // Nullable if soft delete is used
    created_at: string;
    updated_at: string;
}

export interface Application {
    id: string;
    uuid: string;
    applicant_id: string;
    center_id: string;
    current_status: string;
    application_type_id: string;
    application_for_id: string;
    missal_no: string;
    entry_datetime: string;
    remarks: string;
    amount: string;
    personal_image: string;
    on_desk: string;
    guardian_type_id: string;
    tehsil_id: string;
    district_id: string;
    region_id: string;
    created_by: string;
    updated_by: string;
    deleted_by: string;
    deleted_at: string;
    created_at: string;
    updated_at: string;
}

export interface Appointment {
    id: string;
    application_id: string;
    qmatic_token: string;
    appointment_date: string;
    appointment_time: string;
    delivery_date: string;
    created_at: string;
    updated_at: string;
}

export interface ApplicationBiometric {
    id: string;
    application_id: string;
    finger_type: string;
    image_path: string;
    feature_set: string;
    mime_type: string;
    created_by: string;
    updated_by: string;
    deleted_by: string;
    deleted_at: string;
    created_at: string;
    updated_at: string;
}
