{{-- <h2>Application Status Updated</h2>

<p>Dear {{ $application->full_name }},</p>

<p>Your application has been updated.</p>

<p><b>Updated By:</b> {{ $actionBy }}</p>

<p><b>Current Status:</b> {{ $application->current_status }}</p>

<p>Thank you.</p> --}}

{{-- <!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Application Status Update</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">

    <h2 style="color: #2c3e50;">Application Status Update Notification</h2>

    <p>Dear {{ $application->applicant->full_name }},</p>

    <p>
        This is to inform you that your <strong>{{ $application->certificate_type }}</strong> application submitted under the AJK Digitalize System
        has been reviewed and its status has been updated.
    </p>

    <table style="border-collapse: collapse; width: 100%; max-width: 600px;">
        <tr>
            <td style="padding: 8px; font-weight: bold;">Application Reference:{{ $application->missal_no }}</td>
        </tr>

        <tr>
            <td style="padding: 8px; font-weight: bold;">Updated By:{{ $actionBy }}</td>
        </tr>

        <tr>
            <td style="padding: 8px; font-weight: bold;">Current Status:{{ $application->current_status }}</td>
        </tr>
    </table>

    <p style="margin-top: 20px;">
        Please note that you may log in to your account to view detailed information or further updates regarding your application.
    </p>

    <p>
        If you have any queries, kindly contact the concerned office.
    </p>

    <br>

    <p>Regards,</p>
    <p><b>AJK Domicile System</b></p>

</body>
</html> --}}


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Application Status </title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color:#f9f9f9; padding:20px;">

    <div style="max-width:600px; margin:auto; background:#ffffff; padding:20px; border:1px solid #ddd; border-radius:8px;">

        <h2 style="color:#2c3e50; margin-bottom:10px;">
            Application Status 
        </h2>

        <p>Dear <strong>{{ $application->applicant->full_name ?? 'Applicant' }}</strong>,</p>

        <p>
            This is to inform you that your <strong>{{ $application->certificate_type }}</strong> application
            submitted under the AJK Digitalization System
            has been processed and its status has been updated.
        </p>
    @php
    $forwardMessage = '';

    if( $status === 'Delivered' ){
        $forwardMessage = 'delivered successfully. You are advised to check your email or contact your respective center if not received.';
    }
    elseif($actionBy === 'DEO' || $actionBy === 'Center In-charge'){
        $forwardMessage = 'forwarded to the Assistant Commissioner (AC)';
    } 
    elseif($actionBy === 'AC' || $actionBy === 'ACR'){
        $forwardMessage = 'forwarded to the Deputy Commissioner (DC)';
    } 
    elseif($actionBy === 'DC'){
        $forwardMessage = 'approved. You are advised to contact your respective center';
    } 
    else{
        $forwardMessage = 'updated';
    }
@endphp

<p>
    Your application has been received by 
    <strong>{{ $actionBy ?? 'System' }}</strong> 
    and {{ $forwardMessage }}.
</p>
        


        <p style="margin-top:20px;">
            Your application is being processed as per the official verification workflow.
            You may log in to your account to track further updates.
        </p>

        <p>
            If you require any assistance, please contact the concerned office.
        </p>

        <br>

        <p>Regards,</p>
        <p><strong>AJK Domicile System</strong></p>

    </div>

</body>
</html>