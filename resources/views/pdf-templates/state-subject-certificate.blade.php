@php
use Carbon\Carbon;
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
            color: #1443bb;
        }

        /* .certificate {
            margin: 10px 10px 5px 10px;
            border: 6px double #1443bb;
            padding: 20px;
            min-height: 92vh;
            position: relative;
        } */

        .certificate {
            margin: 10px 10px 5px 10px;
            border: 6px solid #1443bb;
            /* outer thick border */
            padding: 12px 20px;
            position: relative;
        }

        .certificate::before {
            content: "";
            position: absolute;
            top: 5px;
            left: 5px;
            right: 5px;
            bottom: 5px;
            border: 2px solid #1443bb;
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
            border-bottom: 1px solid #1443bb;
            padding: 0 10px;
            text-align: center;
        }

        .sign-table {
            width: 50%;
            margin-top: 20px;
        }

        .logo-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 150px;
            height: 150px;
            border: 1px solid #000;
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
            margin-bottom: 8px;
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
            border: 1px solid #000;
            overflow: hidden;
        }


        /* .footer-info {
            width: 100%;
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            padding: 5px 15px;
            margin-top: 5px;
        } */
        .footer-info {
            position: fixed;
            bottom: 0px;
            left: 0;
            right: 0;
            padding: 0px 20px;
            font-size: 14px;
            /* display: flex; */
            justify-content: space-between;
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
            border: 1px solid #1443bb;
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
            text-align: start;
        }

        .particular-heading {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .personal-image {
            width: 130px;
            height: 130px;
            overflow: hidden;
            border: 1px solid #1443bb;
            border-radius: 5px;
            margin: 0 auto 0 0;
        }

        .personal-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .domicile-header-table {
            width: 100%;
        }

        .biometric-image-cell {
            width: 20%;
            border: 1px solid #1443bb;
            height: 60px;
            text-align: center;
            vertical-align: middle;
        }

        .duplicate-text {
            font-size: 18px;
            font-weight: bolder;
            color: red;
            text-align: center;
            margin-bottom: 2px;
        }

        .child-row td {
            padding: 0 !important;
            font-size: 8px;
            line-height: 8px;
            text-align: center;
            vertical-align: middle;
        }

        .line {
            width: 80%;
            margin: 0 auto 0;
            border-bottom: 1px solid #1443bb;
        }

        .line-age {
            width: 60%;
            margin: 0 auto 0;
            border-bottom: 1px solid #1443bb;
        }
        .empty-child {
            width: 80%;
            height: 8px;
            margin: 0 auto;
        }
    </style>
    <title>{{ 'State Subject Certificate : '. $application->first_name }}</title>
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
                            {{-- <span class="form-no"> فارم : <u>A</u></span> --}}
                            {!! $qrCode !!}
                        </div>
                    </td>

                    <td
                        style="width: 70%; flex-direction: column; gap: 2px; align-items: center; justify-content: center;">
                        {{-- <div class="title-wrapper">
                            <img src="{{ $header }}" alt="state-subject-certificate" style="width: 60%">
                        </div> --}}

                        <div class="title-wrapper">
                            <p>(فارم A-1)</p>
                            <p class="title"> آزاد جموں و کشمیر کونسل</p>
                            <p class="font-nastaleeq rule-box">
                                <span style=" border: 1px solid; padding-inline: 5px">(قواعد باشندہ ریاست آزاد جموں و
                                    کشمیر مجریہ۱۹۸۰ کا قاعدہ نمبر ۴ ملاحظہ ہو)</span>
                            </p>
                            <p class="sub-title" style="font-size: 30px;">سرٹیفکیٹ باشندہ ریاست جموں و کشمیر</p>
                        </div>
                    </td>

                    <td class="right-col" style="width: 15%">
                        <div class="personal-image">
                            <img src="{{ $image }}" alt="personal-image">
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- ================= BODY ================= -->
        <table style="width: 100%; margin-top: 20px" class="domicile-body-table">
            <tbody>
                <tr>
                    <td style="width: 20%">تصدیق کیا جاتا ہے کہ مسمی:</td>
                    <td style="width: 30%; border-bottom: 1px solid #1443bb;">{{ $application->applicant->full_name }}
                    </td>
                    <td style="width: 15%">ولد/دختر/زوجہ/بیوہ:</td>
                    <td style="width: 35%; border-bottom: 1px solid #1443bb;">{{ $application->applicant->full_name }}
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="w-full">
            <tbody>
                <tr>
                    <td style="width: 50px">ساکن:</td>
                    <td style="border-bottom: 1px solid #1443bb; width: 150px">{{
                        $application->applicant->residence_place }}</td>
                    <td style="width: 60px">تحصیل:</td>
                    <td style="width: 150px; border-bottom: 1px solid #1443bb;">{{ $application->applicant->tehsil->name
                        }}</td>
                    <td style="width: 50px">ضلع:</td>
                    <td style="width: 150px; border-bottom: 1px solid #1443bb">{{
                        $application->applicant->district->name }}</td>
                </tr>
            </tbody>
        </table>

        <table class="w-full">
            <tbody>
                <tr>
                    <td style="width: 140px">بروئے تحقیقات برمثل:</td>
                    <td style="width: 150px; border-bottom: 1px solid #1443bb;">{{ $application->missal_no }}</td>
                    <td style="width: 150px;">باشندہ ریاست جموں و کشمیر درجہ:</td>
                    <td style="width: 150px; border-bottom: 1px solid #1443bb;">{{
                        $application->applicant->state_subject_class }}</td>
                    <td>ہے۔</td>
                </tr>
            </tbody>
        </table>

        <table class="w-full">
            <tbody>
                <tr>
                    <td class="font-nastaleeq" width="140"> لہذا قانون باشندہ ریاست جموں و کشمیر مجریہ ۱۹۸۰ء کے تحت
                        سرٹیفکیٹ
                        جاری کیا جاتا ہے۔
                    </td>
                </tr>
            </tbody>
        </table>
        <!-- ================= SIGN ================= -->
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
                            @endphp --}}

                            @if($dcApproval?->esign)
                            <div
                                style="width: 200px; height: 50px; margin: 0 auto; display: flex; align-items: center; justify-content: center; border-bottom:  background: #fff;">
                                <img src="{{ storage_path('app/public/' . $dcApproval->esign) }}"
                                    style="max-width: 100%; max-height: 100%; width: auto; height: auto; object-fit: contain;"
                                    alt="dc-sign" />
                            </div>
                            @else
                            <span>No DC Sign</span>
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
            <span class="particular-heading">تفصیل درخواست دہندہ</span>
        </div>

        <table>
            <tbody>
                <tr>
                    <td style="width: 10%">
                        <span style="white-space: nowrap;  padding-inline: 5px"> نام:</span>
                    </td>
                    <td style="width: 40%; border-bottom: 1px solid #999494;">{{ $application->applicant->full_name }}
                    </td>
                    <td style="width: 10%">
                        <span style="white-space: nowrap; padding-inline: 5px">ولد/دختر/زوجہ/بیوہ:</span>
                    </td>
                    <td style="width: 40%; border-bottom: 1px solid #1443bb;">{{
                        $application->applicant->wife_husband_name }}
                    </td>
                </tr>
            </tbody>
        </table>

        <table>
            <tbody>
                <tr>
                    <td style="width: 10%">
                        <span style="white-space: nowrap;">پتہ</span>
                    </td>
                    <td style="width: 90%; border-bottom: 1px solid #1443bb; font-size: 16px">
                        <span>{{ $application->applicant->address }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
        <table>
            <tbody>
                <tr>
                    <td style="width: 15%">ازدواجی حیثیت:</td>
                    <td style="width: 35%; border-bottom: 1px solid #1443bb">{{
                        optional($application->applicant->maritalStatus)->name }}
                    </td>
                    <td style="width: 15%">بیوی/شوہر کا نام:</td>
                    <td style="width: 35%; border-bottom: 1px solid #1443bb">{{
                        $application->applicant->wife_husband_name ?? 'N/A' }}</td>
                    {{-- <td style="width: 15%">شناختی کارڈ نمبر:</td>
                    <td style="width: 18%; border-bottom: 1px solid #1443bb; font-size: 16px; text-align: center;">{{
                        $application->applicant->identity_number ?? '' }}</td> --}}
                </tr>
            </tbody>
        </table>

        <table class="w-full">
            <tbody>
                <tr>
                    <td style="width: 10%">تاریخ پیدائش:</td>
                    <td style="width: 40%; border-bottom: 1px solid #1443bb;">
                        <span dir="ltr">{{ $application->applicant->dob?
                            Carbon::create($application->applicant->dob)->format('d-m-Y'): '' }}</span>
                    </td>
                    <td style="width: 20%">بچوں کے نام اور اُنکی
                        عمریں:</td>
                    <td style="width: 30%; border-bottom: 1px solid #1443bb;"></td>
                </tr>
            </tbody>
        </table>

        {{-- <table class="w-full">
            <tbody>
                <tr>
                    <td style="width: 10%">شناختی علامت</td>
                    <td style="border-bottom: 1px solid #1443bb">{{ $application->applicant->identity_symbol ?? '' }}
                    </td>
                </tr>
            </tbody>
        </table> --}}

        <div
            style="width:100%; display: flex; justify-content: space-between; align-items: flex-start; margin-top: 30px;">
            <div style="width: 45%">
                <table style="width: 100%">
                    <tbody>
                        <tr>
                            <td style="width: 100%">
                                <span style="white-space: nowrap; font-weight: bold;">دستخط یا نشان انگوٹھا</span>
                            </td>
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
                        $maxRows = 7;
                        $count = count($children);
                        @endphp

                        {{-- @foreach($application->applicant->children as $child)
                        <tr>
                            <td style="text-align: center; padding: 3px;">
                                <span style="font-size: 12px;"> {{ $child->name }}</span>
                                <div style="width: 80%; margin: 5px auto 0; border-bottom: 1px solid #1443bb;"></div>
                            </td>

                            <td style="text-align: center; padding: 3px;">
                                <span style="font-size: 12px;">{{ $child->age }} سال</span>
                                <div style="width: 60%; margin: 5px auto 0; border-bottom: 1px solid #1443bb;"></div>
                            </td>
                        </tr>
                        @endforeach --}}

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

                            {{-- @if(count($application->applicant->children)===0) --}}
                            {{-- @for($i = $count; $i < $maxRows; $i++) <tr>
                                <td style="padding: 3px;">
                                    <div style="width: 80%; margin: 5px auto 0; border-bottom: 1px solid #1443bb;">
                                    </div>
                                </td>

                                <td style="padding: 3px;">
                                    <div style="width: 60%; margin: 5px auto 0; border-bottom: 1px solid #1443bb;">
                                    </div>
                                </td>
                                </tr>
                                @endfor --}}
                                {{-- @endif --}}

                    </tbody>
                </table>
            </div>
        </div>

        <div style="width: 100%; display: flex; justify-content: space-between; align-items: flex-start;">
            <table style="width: 40%; margin-top: 10px">
                <tbody>
                    <tr>
                        <td style="width: 100px">پيشہ:</td>
                        <td style="width: 200px; border-bottom: 1px solid #1443bb">{{ $application->signature_thumb }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 100px">شناختی علامت:</td>
                        <td style="width: 200px; border-bottom: 1px solid #1443bb">{{ $application->signature_thumb }}
                        </td>
                    </tr>

                </tbody>
            </table>
            <table style="width: 40%; margin-top: 30px">
                <tbody>
                    <tr>
                        <td></td>
                        <td style="width: 200px; font-size: 14px;">بائیں ہاتھ کے انگوٹھا یا انگلیوں کے نشان
                            <br><small>(خواتین کے
                                دائیں ہاتھ کے انگوٹھا/ انگلیوں کے نشان)</small>
                        </td>
                    </tr>

                </tbody>
            </table>


        </div>

        <div>
            <table style="width: 100%; margin-top: 15px; border-collapse: collapse;">
                <tbody>

                    <tr>
                        @foreach(['thumb','index','middle','ring','little'] as $finger)
                        <td class="biometric-image-cell">
                            @if(!empty($fingerprints[$finger]))
                            <img src="{{ $fingerprints[$finger] }}" alt="finger-print"
                                style="width:80px; height:60px; object-fit:contain;">
                            @else
                            <span style="font-size: 10px; "></span>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="width: 100%; display: flex; justify-content: space-around; align-items: center; margin-top: 15px">
            <!-- Left -->
            <div style="width: 33%">
                {{-- <img src="{{ $logo }}" alt="efc-logo" height="80"> --}}
            </div>

            <!-- Center -->
            <div style="width: 33%"></div>

            <!-- Right empty -->
            <table style="width: 33%">
                <tbody>
                    <tr>
                        <td style="align-items: center">دستخط</td>
                        {{-- <td style="width: 200px; border-bottom: 1px solid #1443bb; text-align: center;"></td> --}}
                        <td style="text-align: center;">
                            {{-- @php
                            $dcApproval = $application->approvals->first(fn($a) => $a->level === 'DC');
                            @endphp --}}

                            @if($dcApproval?->esign)
                            <div
                                style="width: 200px; height: 50px; margin: 0 auto; display: flex; align-items: center; justify-content: center; border-bottom:  background: #fff;">
                                <img src="{{ storage_path('app/public/' . $dcApproval->esign) }}"
                                    style="max-width: 100%; max-height: 100%; width: auto; height: auto; object-fit: contain;"
                                    alt="dc-sign" />
                            </div>
                            @else
                            <span>No DC Sign</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center; padding-top: 2px;">
                            ڈسٹرکٹ مجسٹریٹ ضلع {{ $districtName ?? 'N/A' }} آزادکشمير
                        </td>
                    </tr>
                    <tr>
                        <td style="align-items: center"> تاریخ</td>
                        <td style="width: 200px; border-bottom: 1px solid #1443bb; text-align: center;">{{
                            $application->created_at->format('d-m-Y') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer-info">
        <span>CNIC: {{ $application->applicant->identity_number }}</span>
        {{-- <span>Tracking Number: {{ $application->tracking_token_no }}</span> --}}
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