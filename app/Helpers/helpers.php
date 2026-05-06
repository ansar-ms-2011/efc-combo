
<?php

use App\Models\Type;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

if(!function_exists('extractImageData')){
    function extractImageData($base64File)
    {
        // Match general base64 data URLs: data:[mime-type];base64,[data]
        if (!preg_match('/^data:([\w\/\-\+\.]+);base64,/', $base64File, $matches)) {
            return null;
        }

        $mimeType = $matches[1]; // e.g., application/pdf, image/png
        $base64String = preg_replace('/^data:[\w\/\-\+\.]+;base64,/', '', $base64File);
        $base64String = str_replace(' ', '+', $base64String);

        $decodedData = base64_decode($base64String);

        // Extract extension from MIME type
        $mimeMap = [
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'audio/mpeg' => 'mp3',
            'video/mp4' => 'mp4',
            'application/zip' => 'zip',
            // Add more as needed
        ];

        $extension = $mimeMap[$mimeType] ?? null; // fallback if MIME unknown

        return [
            'extension' => $extension,
            'data' => $decodedData,
            'mime_type' => $mimeType,
        ];
    }
}


if (!function_exists('format_date')) {
    function format_date($date, $format = 'd-m-Y')
    {
        return \Carbon\Carbon::parse($date)->format($format);
    }
}

if (!function_exists('formatDateYmd')) {
    function formatDateYmd($date, $format = 'Y-m-d')
    {
        return $date ? \Carbon\Carbon::parse($date)->format($format) : null;
    }
}



if (!function_exists('getChildTypes')) {
    function getChildTypes($group)
    {
        return Type::whereHas('parent', function ($query) use ($group) {
            $query->where('name', 'LIKE', $group);
        })->orderBy('order')->get();
    }
}

if(!function_exists('generateRandomNo')){
    // Generate random number for order tracking
    function generateRandomNo($table,$column,$length,$prefix=null){
        return IdGenerator::generate([
            'table' => $table,
            'field' => $column,
            'prefix' => $prefix ?? '1',
            'length'=>$length
        ]);
    }
}


if(!function_exists('generateVoucherCode')){
    // Generate random number for order tracking
    function generateVoucherCode($table,$column,$length,$prefix=null,$year=Null){
        return IdGenerator::generate([
            'table' => $table,
            'field' => $column,
            'prefix' => $prefix.$year.'-' ?? '1-'.$year,
            'length'=>$length,
            'reset_on_prefix_change' => true, // only works in newer versions
        ]);
    }
}

if(!function_exists('getAccountAmount')){
    function getAccountAmount($chart_of_account_id)
    {
        $query = TransactionDetail::where('chart_of_account_id', $chart_of_account_id);
        // $data['petty_cash_account_id'] = $chart_of_account_id;
        $data['total_amount'] = (clone $query)->sum('amount');
        $data['credit_amount'] = (clone $query)->where('type', 'CREDIT')->sum('amount');
        $data['debit_amount'] = (clone $query)->where('type', 'DEBIT')->sum('amount');
        $data['balance'] = $data['debit_amount'] - $data['credit_amount'];
        return $data;
    }
}


if(!function_exists('getChartOfAccountName')){
    function getChartOfAccountName($chart_of_account_id)
    {
        $chartOfAccount = ChartOfAccount::where('id', $chart_of_account_id)->first();
        if (!$chartOfAccount) {
            return null;
        }
        return $chartOfAccount;
    }
}

if(!function_exists('ItemhasAmount')){
    function ItemhasAmount($item,$type)
    {
            $each_bank_has_amount = TransactionDetail::where('chart_of_account_id', $item->chart_of_account_id)
            ->where('type',$type)->sum('amount');
            if($each_bank_has_amount < $item->amount){
                return [
                    'success' => false,
                    'message' => "Insufficient balance in this account.".getChartOfAccountName($item->chart_of_account_id)->name
                ];
            }
            return [
                'success' => true
            ];
    }
}

function periodFilter($data)
    {
        return  function ($query) use ($data) {
            return $query->when($data['type'] ?? null, function ($query, $type) use ($data) {
                if ($type === 'DAY') {
                    $query->whereDate('date', today());
                } elseif ($type === 'WEEK') {
                    $query->whereBetween('date', [
                        today()->subDays(6)->format('Y-m-d'),
                        today()->format('Y-m-d')
                    ]);
                } elseif ($type === 'MONTH') {
                    $query->whereBetween('date', [
                        today()->subDays(29)->format('Y-m-d'),
                        today()->format('Y-m-d')
                    ]);
                } elseif ($type === 'RANGE') {
                    if (!empty($data['from_date']) && !empty($data['to_date'])) {
                        $fromDate = formatDateYmd($data['from_date']);
                        $toDate = formatDateYmd($data['to_date']);
                        $query->whereBetween('date', [$fromDate, $toDate]);
                    }
                }
            });
        };
    }

if(!function_exists('generateCode')){
    function generateCode($table,$prefix)
    {
        $lastCode= DB::table($table)->where('company_id', Auth::user()->company_id)->orderBy('number', 'desc')->first();

        $current_year = date('y');
        if (!$lastCode) {
            return [
                'code' =>  "$prefix-{$current_year}-0001",
                'number' => 1,
            ];
        }

        $nextNumber = $lastCode->number + 1;
        $leadingZeros = str_pad('', 4 - strlen($nextNumber), '0'); // Ensure 3 digits
        return [
                'code' =>  "$prefix-{$current_year}-{$leadingZeros}{$nextNumber}",
                'number' => $nextNumber,
            ];

    }
//     function generateCode($table, $prefix)
// {
//     $currentYear = date('y');
//     $lastCode = DB::table($table)
//         ->where('company_id', Auth::user()->company_id)
//         ->orderBy('number', 'desc')
//         ->first();

//     if (!$lastCode || substr($lastCode->code, -8, 2) != $currentYear) {
//         return [
//             'code' => "$prefix-{$currentYear}-0001",
//             'number' => 1,
//         ];
//     }

//     $nextNumber = $lastCode->number + 1;
//     $formattedNumber = str_pad($nextNumber, 4, '0', STR_PAD_LEFT); // Ensure 4 digits (0001)

//     return [
//         'code' => "$prefix-{$currentYear}-{$formattedNumber}",
//         'number' => $nextNumber,
//     ];
// }
}

function taxCreation($company_id)
{
    DB::table('taxes')->insert(
        [
            [
                'company_id' => $company_id,
                'name' => 'WHT',
                'description' => 'INCOME TAX (WHT) / SUPPLIER TAX (WHT)',
                'is_withholding' => 1,
                'percentage' => '10.00',
                'fixed_amount' => NULL,
                'type' => 'PERCENTAGE',
                'status' => 'ACTIVE',
                'created_by_id' => Auth::id(),
                'updated_by_id' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => $company_id,
                'name' => 'GST',
                'description' => 'SALES TAX PAYABLE ISLAMABAD',
                'is_withholding' => 0,
                'percentage' => '18.00',
                'fixed_amount' => NULL,
                'type' => 'PERCENTAGE',
                'status' => 'ACTIVE',
                'created_by_id' => Auth::id(),
                'updated_by_id' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => $company_id,
                'name' => 'PST',
                'description' => "PUNJAB SALES TAX PAYABLE - PST",
                'is_withholding' => 0,
                'percentage' => '17.00',
                'fixed_amount' => NULL,
                'type' => 'PERCENTAGE',
                'status' => 'ACTIVE',
                'created_by_id' => Auth::id(),
                'updated_by_id' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => $company_id,
                'name' => 'KPST',
                'description' => "KPK SALES TAX PAYABLE - KPST",
                'is_withholding' => 0,
                'percentage' => '17.00',
                'fixed_amount' => NULL,
                'type' => 'PERCENTAGE',
                'status' => 'ACTIVE',
                'created_by_id' => Auth::id(),
                'updated_by_id' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => $company_id,
                'name' => 'SST',
                'description' => "SINDH SALES TAX PAYABLE - SST",
                'is_withholding' => 0,
                'percentage' => '17.00',
                'fixed_amount' => NULL,
                'type' => 'PERCENTAGE',
                'status' => 'ACTIVE',
                'created_by_id' => Auth::id(),
                'updated_by_id' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]
    );
}



