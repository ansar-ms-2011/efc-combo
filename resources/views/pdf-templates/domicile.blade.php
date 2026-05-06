@php
$fontBase64 = base64_encode(file_get_contents(public_path('fonts/JameelNooriNastaleeqRegular.ttf')));
@endphp

<!DOCTYPE html>
<html lang="ur" dir="rtl">

<head>
    <meta charset="UTF-8">
    <style>
        @font-face {
            font-family: 'jameelnoorinastaliq';
            src: url("data:font/truetype;base64,{{ $fontBase64 }}") format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* body {
            font-family: 'jameelnoorinastaliq', serif;
            direction: rtl;
            text-align: right;
            font-size: 20px;
            margin: 20px 10px;
            border: 6px double #000;
            padding: 20px 20px;
            color: #000;
        } */

        body {
            font-family: 'jameelnoorinastaliq', serif;
            direction: rtl;
            text-align: right;
            font-size: 20px;
            margin: 0;
            padding: 0;
            color: #016c59f3;
        }

        /* .certificate {
            margin: 20px 10px 5px 10px;
            border: 6px double #016c59f3;
            padding: 20px;
            position: relative;
        } */

        .certificate {
            margin: 20px 10px 5px 10px;
            border: 6px solid #016c59f3;
            /* outer thick border */
            padding: 20px;
            position: relative;
        }

        .certificate::before {
            content: "";
            position: absolute;
            top: 5px;
            left: 5px;
            right: 5px;
            bottom: 5px;
            border: 2px solid #016c59f3;
            /* inner thin border */
            pointer-events: none;
        }

        /* Watermark Styles */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            z-index: 9999;
            opacity: 0.3;
            pointer-events: none;
        }

        .watermark-text {
            font-size: 80px;
            font-weight: bold;
            color: #ff0000;
            white-space: nowrap;
            font-family: Arial, sans-serif;
            letter-spacing: 10px;
        }

        .bordered-box {
            margin: 0 10px;
            display: inline-block;
            min-width: 100px;
            min-height: 28px;
            border-bottom: 1px solid #016c59f3;
            padding: 0 10px;
            text-align: center;
        }

        .sign-table {
            width: 50%;
            margin-top: 20px;
        }

        /* .footer-info {
            width: 100%;
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            padding: 0 15px;
            margin: 0 10px;

            } */

        .footer-info {
            position: fixed;
            bottom: 5px;
            left: 0;
            right: 0;
            padding: 0 20px;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
        }

        .logo-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100px;
            height: 100px;
            border: 1px solid #016c59f3;
            overflow: hidden;
        }

        /* .form-no {
            white-space: nowrap;
            font-size: 15px;
            line-height: 18px;
            margin-bottom: 5px;
        } */

        .form-no {
            display: block;
            white-space: nowrap;
            font-size: 15px;
            line-height: 18px;
            margin-bottom: 5px;
            text-align: center;
        }

        .qr-wrapper {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            /* padding: 10px; */
            width: 100px;
            height: 100px;
            border: 1px solid #016c59f3;
            overflow: hidden;

        }

        .title {
            font-size: 24px;
            font-weight: bold;
        }

        .sub-title {
            font-size: 20px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .children-table th,
        .children-table td {
            border: 1px solid #e1dcdc;
        }

        .title-wrapper {
            display: flex;
            flex-direction: column;
            gap: 2px;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .domicile-body-table {
            margin-top: 40px;
        }

        .particular-wrapper {
            width: 100%;
            text-align: center;
        }

        .particular-heading {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .personal-image-wrapper {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .personal-image {
            width: 120px;
            height: 120px;
            overflow: hidden;
            border: 1px solid #016c59f3;
            border-radius: 5px;

        }

        .personal-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .domicile-header-table {
            width: 100%;
            margin-top: 10px;
        }

        .left-col {

            position: relative;
        }

        .duplicate-text {
            font-size: 18px;
            font-weight: bolder;
            color: red;
            text-align: center;
            margin-bottom: 10px;
            position: absolute;
            top: -25px;
            left: 50%;
            transform: translateX(-50%);
        }
        .child-row td {
            /* border: 1px solid red; */
            padding: 0 !important;
            font-size: 8px;
            line-height: 8px;
            text-align: center;
            vertical-align: middle;
        }

        .line {
            width: 80%;
            margin: 0 auto 0;
            border-bottom: 1px solid #016c59f3;
        }

        .line-age {
            width: 60%;
            margin: 0 auto 0;
            border-bottom: 1px solid #016c59f3;
        }
        .empty-child {
            width: 80%;
            height: 8px;
            margin: 0 auto;
        }
    </style>
    <title>{{ 'Domicile : '. $application->first_name }}</title>
</head>

<body>
    <!-- Watermark Element - Only shows when $preview is true -->
    @if(isset($preview) && $preview === true)
    <div class="watermark">
        <div class="watermark-text">PREVIEW ONLY</div>
    </div>
    @endif
    <div id="certificate" class="certificate" dir="rtl">
        <table style="width: 100%" class="domicile-header-table">
            <tbody>
                <tr>
                    <td class="left-col" style="width: 15% text-align:center; vertical-align: top;">
                        @if(isset($duplicate) && $duplicate === true)
                        <div class="duplicate-text">DUPLICATE</div>
                        @endif
                        <span class="form-no"> فارم نمبر : <u>{{ substr($application->tracking_token_no, 0, 15)
                                }}</u></span>
                        <div class="qr-wrapper">

                            {!! $qrCode !!}
                        </div>
                    </td>

                    <td
                        style="width: 70%; flex-direction: column; gap: 2px; align-items: center; justify-content: center;">
                        <div class="title-wrapper">
                            <p>(فارم P-1)</p>
                            <p class="title">تحصیل {{ $tehsilName ?? 'N/A' }}</p>
                            <p class="font-nastaleeq rule-box">
                                <span style=" padding-inline: 5px">(قواعد باشندہ ریاست آزاد جموں و
                                    کشمیر مجریہ۱۹۸۰ کا قاعدہ نمبر ۷ ملاحظہ ہو)</span>
                            </p>
                            <p class="sub-title" style="font-size: 30px">
                                ڈومیسائل سرٹیفکیٹ
                            </p>
                        </div>
                    </td>

                    <td class="right-col" style="width: 15%">
                        <div class="logo-wrapper">
                            <img src="{{ $logo }}" alt="qr-code" style="width: 90%; height: 90%; object-fit: contain;">
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- ================= BODY ================= -->
        <table class="domicile-body-table">
            <tbody>
                <tr>
                    <td>
                        <p style="text-align: justify">
                            مسمی <span class="bordered-box">{{ $application->applicant->full_name }}</span>ولد/دختر/زوجہ/بیوہ:
                            <span class="bordered-box">{{ $application->applicant->wife_husband_name }}</span>نے ازروئے
                            قانون
                            باشندہ ریاست
                            آزاد جموں و کشمیر مجریہ
                            1980ء برائے حصول ڈومیسائل سرٹیفکیٹ درخواست دی ہے۔
                            مندرجہ ذیل کوائف جو کہ درخواست دہندہ سے متعلق ہیں۔ زیر
                            دستخطی ان سے مطمئن ہے۔ اور وہ تمام شرائط جو کہ مذکورہ قانون کی دفعہ 5 کی رو سے بروئے مثل نمبر
                            <span class="bordered-box">{{ $application->missal_no }}</span>
                            مسمی
                            <span class="bordered-box">{{ $application->applicant->full_name }}</span>
                            پر برائے حصول ڈومیسائل سرٹیفکیٹ عائد ہوتی ہیں۔ کو پورا کرتا/
                            کرتی ہے ۔ مذکورہ قانون اور اس کے بنائے گئے قواعد کے مطابق زیردستخطی مسمی
                            <span class="bordered-box">{{ $application->applicant->full_name }}</span>
                            کو ڈومیسائل جاری کرتا ہے۔
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
        <!-- ================= DC SIGNATURE================= -->
        <div style="display: flex; justify-content: left; align-items: center;">
            <table class="sign-table">
                <tbody>
                    <tr>
                        <td style="width: 50%; text-align: center;">
                            <span class="district-maj">ڈسٹرکٹ مجسٹریٹ</span>
                        </td>
                        <td style="text-align: center;">
                            {{-- @php
                            $dcApproval = $application->approvals->first(fn($a) => $a->level === 'DC');
                            $acApproval = $application->approvals->first(fn($a) => $a->level === 'AC');
                            $acrApproval = $application->approvals->first(fn($a) => $a->level === 'ACR');

                            $districtName = $dcApproval?->officer?->district?->urdu_name;
                            $tehsilName = $acApproval?->officer?->tehsil?->urdu_name ??
                            $acrApproval?->officer?->tehsil?->urdu_name;
                            @endphp --}}

                            @if($dcApproval?->esign)
                            <div
                                style="width: 200px; height: 50px; margin: 0 auto; display: flex; align-items: center; justify-content: center; border-bottom:  background: #fff;">
                                <img src="{{ storage_path('app/public/' . $dcApproval->esign) }}"
                                    style="max-width: 100%; max-height: 100%; width: auto; height: auto; object-fit: contain;"
                                    alt="dc-sign" />
                            </div>
                            @else
                            <span>---------------</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center;">
                            <span class="district-name">ضلع {{ $districtName ?? 'N/A' }}</span>
                        </td>

                        <td style="width: 50%; text-align: center;">
                            {{-- <span>دستخط / مہر</span> --}}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="particular-wrapper">
            <span class="particular-heading"> درخوست دہندہ سے متعلقہ کوائف</span>
        </div>

        <table>
            <tbody>
                <tr>
                    <td style="width: 15%">
                        <span style="white-space: nowrap;  padding-inline: 5px">مکمل نام</span>
                    </td>
                    <td style="width: 25%; border-bottom: 1px solid #016c59f3;">{{ $application->applicant->full_name }}
                    </td>
                    <td style="width: 10%">
                        <span style="white-space: nowrap; padding-inline: 5px">ولد/دختر/زوجہ/بیوہ:</span>
                    </td>
                    <td style="width: 40%; border-bottom: 1px solid #016c59f3;">{{
                        $application->applicant->wife_husband_name }}
                    </td>
                </tr>
            </tbody>
        </table>

        <table>
            <tbody>
                <tr>
                    <td style="width: 15%">
                        <span style="white-space: nowrap;">پتہ</span>
                    </td>
                    <td style="width: 85%; border-bottom: 1px solid #016c59f3; font-size: 16px;">
                        <span>{{ $application->applicant->address }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
        <table>
            <tbody>
                <tr>
                    <td style="width: 15%">
                        <span style="white-space: nowrap; padding-inline: 5px"> ڈومیسائل کی جگہ</span>
                    </td>
                    <td style="width: 34%; border-bottom: 1px solid #016c59f3;">
                        <span>{{ $application->applicant->location }}</span>
                    </td>
                    <td style="width: 8%; padding-inline: 5px">
                        <span>تحصیل</span>
                    </td>
                    <td style="width: 17%; border-bottom: 1px solid #016c59f3;">
                        <span>{{ $application->applicant->tehsil->name }}</span>
                    </td>
                    <td style="width: 8%; padding-inline: 5px">
                        <span>ضلع</span>
                    </td>
                    <td style="width: 17%; border-bottom: 1px solid #016c59f3;">
                        <span>{{ $application->applicant->district->name }}</span>
                    </td ></tr>
            </tbody>
        </table>

        <table>
            <tbody>
                <tr>
                    <td style="width: 15%;">
                        <span style="white-space: nowrap;">ازدواجی حیثیت</span>
                    </td>
                    <td style="width: 35%; border-bottom: 1px solid #016c59f3;">{{
                        $application->applicant->maritalStatus->name }}</td>
                    <td style="width: 15%">
                        <span style="white-space: nowrap">بیوی/شوہر کا نام</span>
                    </td>
                    <td style="width: 35%; border-bottom: 1px solid #016c59f3;">
                        {{ $application->applicant->wife_husband_name }}
                    </td>
                </tr>
            </tbody>
        </table>

        <div
            style="width:100%; display: flex; justify-content: space-between; align-items: flex-start; margin-top: 40px;">
            <div style="width: 45%">
                <table style="width: 100%">
                    <tbody>
                        <tr>
                            <td style="width: 20%">
                                <span style="white-space: nowrap;">دستخط</span>
                            </td>
                            <td style="width: 80%; border-bottom: 1px solid"></td>
                        </tr>
                        <tr>
                            <td style="width: 20%">
                                <span style="white-space: nowrap">پیشہ</span>
                            </td>
                            <td style="width: 80%; border-bottom: 1px solid">{{ $application->applicant->occupation }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%">
                                <span style="white-space: nowrap;">شناختی علامت</span>
                            </td>
                            <td style="width: 80%; border-bottom: 1px solid">{{ $application->applicant->identity_symbol
                                }}</td>
                        </tr>
                    </tbody>
                </table>

                <table style="width: 100%; margin-top: 40px;">
                    <tbody>
                        <tr>
                            <td style="width: 20%">
                                <span>دستخط</span>
                            </td>
                            <td style="width: 80%; border-bottom: 1px solid">
                            @if($acApproval?->esign)
                            <div
                                style="width: 200px; height: 50px; margin: 0 auto; display: flex; align-items: center; justify-content: center; border-bottom:  background: #fff;">
                                <img src="{{ storage_path('app/public/' . $acApproval->esign) }}"
                                    style="max-width: 100%; max-height: 100%; width: auto; height: auto; object-fit: contain;"
                                    alt="ac-sign" />
                            </div>
                            @else
                            <span>---------------</span>
                            @endif    
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%">نام</td>
                            <td style="width: 80%; border-bottom: 1px solid">{{ $acName ?? $acrName ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="width: 20%">عہدہ</td>
                            <td style="width: 80%; border-bottom: 1px solid">

                                @if($acApproval)
                                <span class="font-nastaleeq">ایس ڈی پی </span>
                                @elseif($acrApproval)
                                <span class="font-nastaleeq">ایس ڈی آر </span>
                                @endif

                                {{ $tehsilName ?? 'N/A' }}

                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%">تاریخ</td>
                            <td style="width: 80%; border-bottom: 1px solid">&nbsp; {{ $signdate ?? 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div style="width: 45%;">
                 <table style="width: 100%; border-collapse: collapse;">
             <tbody>

             <!-- Header -->
             <tr style="font-size: 16px;">
                <th style="width: 50%; text-align: center; padding: 2px;">
                    بچوں کے نام
                    <div class="line"></div>
                </th>

                <th style="width: 50%; text-align: center; padding: 2px;">
                    عمر
                    <div class="line-age"></div>
                </th>
            </tr>

             @php
                        $children = $application->applicant->children ?? [];
                        $maxRows = 5;
                        $count = count($children);
                        @endphp
             
              @foreach($children as $child)
                        <tr class="child-row">
                            <td>
                                {{ $child->name ?? '' }}
                                <div class="line"></div>
                            </td>

                            <td>
                                {{ $child->age ?? '' }} سال
                                <div class="line-age"></div>
                            </td>
                        </tr>
                        @endforeach
                        @for($i = $count; $i < $maxRows; $i++) <tr class="child-row">
                            <td>
                                <div class="empty-child"></div>
                                <div class="line"></div>
                            </td>

                            <td>
                                <div class="empty-child"></div>
                                <div class="line-age"></div>
                            </td>
                            </tr>
                            @endfor

            {{-- @foreach($application->applicant->children as $child)
            <tr>
                <td style="text-align: center; padding: 3px;">
                    {{ $child->name }}
                    <div style="width: 80%; margin: 3px auto 0; border-bottom: 1px solid #016c59f3;"></div>
                </td>

                <td style="text-align: center; padding: 3px;">
                    {{ $child->age }} سال
                    <div style="width: 60%; margin: 3px auto 0; border-bottom: 1px solid #016c59f3;"></div>
                </td>
            </tr>
            @endforeach

            @if(count($application->applicant->children)===0)
                @for($i=0; $i<5; $i++)
                <tr>
                    <td style="padding: 3px;">
                        <div style="width: 80%; margin: 10px auto 0; border-bottom: 1px solid #016c59f3;"></div>
                    </td>

                    <td style="padding: 3px;">
                        <div style="width: 60%; margin: 10px auto 0; border-bottom: 1px solid #016c59f3;"></div>
                    </td>
                </tr>
                @endfor
            @endif --}}

        </tbody>
    </table>

                <table style="width: 100%; margin-top: 20px;">
                    <tbody>
                        @if($image)
                        <tr>
                            <td style="width: 100%;" align="start">
                                <div class="personal-image-wrapper">
                                    <div class="personal-image">
                                        <img src="{{ $image }}" alt="personal-image">
                                    </div>
                                    <span style="font-size: 14px">درخواست دہندہ کی تصویر</span>
                                </div>
                            </td>
                        </tr>
                        @endif


                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="footer-info">
        <span>CNIC: {{ $application->applicant->identity_number }}</span>

    </div>

</body>
{{-- <table>
    <tbody>
        <tr>


            <td>

                <span>{{ $application->applicant->identity_number }}:CNIC</span>
            </td>

            <td>

                <span>Tracking Number:{{ $application->tracking_token_no }}</span>
            </td>
        </tr>
    </tbody>
</table> --}}

</html>