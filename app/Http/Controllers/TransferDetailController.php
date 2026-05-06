<?php

namespace App\Http\Controllers;

use App\Models\TransferDetail;
use App\Models\User;
use App\Models\ServiceCenter;
use App\Models\ServiceCenterUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferDetailController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],

            'to_region_id'   => ['nullable', 'exists:demographies,id'],
            'to_district_id' => ['nullable', 'exists:demographies,id'],
            'to_tehsil_id'   => ['nullable', 'exists:demographies,id'],
            'to_center_id'   => ['nullable', 'exists:centers,id'],

            'service_ids'    => ['nullable', 'array'],
            'service_ids.*'  => ['exists:services,id'],

            'posting_letter' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ]);

        DB::beginTransaction();

        try {

            $user = User::findOrFail($request->user_id);

            /* =========================
               CURRENT LOCATION (FROM)
            ==========================*/
            $from = [
                'region_id'   => $user->region_id,
                'district_id' => $user->district_id,
                'tehsil_id'   => $user->tehsil_id,
                'center_id'   => $user->center_id,
            ];

            /* =========================
               TARGET LOCATION (TO)
            ==========================*/
            $to = [
                'region_id'   => $request->to_region_id   ?? $from['region_id'],
                'district_id' => $request->to_district_id ?? $from['district_id'],
                'tehsil_id'   => $request->to_tehsil_id   ?? $from['tehsil_id'],
                'center_id'   => $request->to_center_id   ?? $from['center_id'],
            ];

            /* =========================
               ROLE RULES
            ==========================*/
            $role = $user->role?->name;

            if ($role === 'Commissioner') {
                $from['district_id'] = null;
                $from['tehsil_id'] = null;

                $to['district_id'] = null;
                $to['tehsil_id'] = null;
            }

            if (in_array($role, ['DC','AC','ACR'])) {
                $to['region_id'] = $from['region_id'];
            }

            if (in_array($role, ['DEO','Center In-charge'])) {
                $to['region_id'] = $from['region_id'];
                $to['district_id'] = $from['district_id'];
            }

            /* =========================
               FILE UPLOAD
            ==========================*/
            $filePath = null;

            if ($request->hasFile('posting_letter')) {
                $filePath = $request->file('posting_letter')
                    ->store('transfers', 'public');
            }

            /* =========================
               SAVE TRANSFER HISTORY
            ==========================*/
            $transfer = TransferDetail::create([
                'user_id' => $user->id,
                'created_by' => auth()->id(),

                'from_region_id' => $from['region_id'],
                'from_district_id' => $from['district_id'],
                'from_tehsil_id' => $from['tehsil_id'],
                'from_center_id' => $from['center_id'],

                'to_region_id' => $to['region_id'],
                'to_district_id' => $to['district_id'],
                'to_tehsil_id' => $to['tehsil_id'],
                'center_id' => $to['center_id'],

                'posting_letter' => $filePath,
            ]);

            /* =========================
               SAVE TRANSFER SERVICES HISTORY
            ==========================*/
            $serviceIds = $request->input('service_ids', []);

            if (!is_array($serviceIds)) {
                $serviceIds = [$serviceIds];
            }

            if (!empty($serviceIds)) {
                $transfer->services()->sync($serviceIds);
            }

            /* =========================
               UPDATE USER LOCATION
            ==========================*/
            $user->update($to);

            /* =========================
               UPDATE SERVICE CENTER MAPPING (SAFE SYNC)
            ==========================*/

            ServiceCenterUser::where('user_id', $user->id)->delete();

            if (!empty($serviceIds) && $to['center_id']) {

                $serviceCenterIds = ServiceCenter::where('center_id', (int) $to['center_id'])
                    ->whereIn('service_id', $serviceIds)
                    ->pluck('id');

                $insertData = $serviceCenterIds->map(function ($scId) use ($user) {
                    return [
                        'user_id' => $user->id,
                        'service_center_id' => $scId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->toArray();

                if (!empty($insertData)) {
                    ServiceCenterUser::insert($insertData);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transfer completed successfully'
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}