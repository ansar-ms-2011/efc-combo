<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserFormRequest;
use App\Models\Center;
use App\Models\Demography;
use App\Models\Employee;
use App\Models\ServiceCenter;
use App\Models\User;
use Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Throwable;

class UserController extends Controller
{
    // List all users
    public function index(Request $request)
    {
        try {
            $query = User::with([
                'employee:id,user_id,cnic',
                'roles:id,name',
                'center:id,name',
                'district:id,name',
                'region:id,name',
                'serviceCenters.service:id,name',
                'tehsil' => function ($query) {
                    $query->select('id', 'name', 'parent_id');
                    $query->with(['district:id,name']);
                },
            ])
                ->whereDoesntHave('roles', function ($q) {
                    $q->where('name', 'Super Admin');
                });

            $authUser = Auth::user();
            $role = $authUser->roles->first()?->name;

            if ($role === 'Center In-charge') {
                $query->where('center_id', $authUser->center_id)
                    ->whereHas('roles', function ($q) {
                        $q->where('name', 'DEO');
                    });
            }

            // search filters

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($request->has('center_ids')) {
                $query->whereIn('center_id', $request->center_ids);
            }

            return response()->json([
                'success' => true,
                'data' => $query->get()
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Create
//     public function store(UserFormRequest $request)
//     {
//         try {
//             DB::beginTransaction();


//             // Create User
//             $user = User::create([
//                 'prefix' => $request->prefix,
//                 'first_name' => $request->first_name,
//                 'last_name' => $request->last_name,
//                 'email' => $request->email,
//                 'password' => bcrypt($request->password),
//                 'region_id' => $request->region_id,
//                 'district_id' => $request->district_id,
//                 'tehsil_id' => $request->tehsil_id,
//                 'center_id' => $request->center_id,
//                 'is_active' => $request->is_active ? 1 : 0,
//             ]);

//             $user->roles()->sync($request->role_id);
//             // Find service_center
// $serviceCenter = DB::table('service_centers')
//     ->where('center_id', $request->center_id)
//     ->where('service_id', $request->service_id)
//     ->first();

// if ($serviceCenter) {
//     DB::table('service_center_users')->insert([
//         'user_id' => $user->id,
//         'service_center_id' => $serviceCenter->id,
//         'created_at' => now(),
//         'updated_at' => now(),
//     ]);
// }

//             // Create Employee
//             Employee::create([
//                 'user_id' => $user->id,
//                 'cnic' => $request->cnic,
//                 'phone_no' => $request->phone_no,
//                 'address' => $request->address,
//                 'center_id' => $request->center_id,
//                 'designation_id' => $request->role_id,
//                 'created_by' => Auth::id(),
//                 'updated_by' => Auth::id(),
//             ]);
            

//             //Save Sign file if present in request
//             $this->saveSignFile($request, $user);

//             DB::commit();

//             return response()->json([
//                 'success' => true,
//                 'message' => 'User created successfully',
//                 'data' => $user
//             ], 201);
//         } catch (\Throwable $th) {
//             DB::rollBack();
//             Log::error($th->getMessage());

//             return response()->json([
//                 'success' => false,
//                 'message' => 'Error creating user',
//                 'error' => $th->getMessage()
//             ], 500);
//         }
//     }

public function store(UserFormRequest $request)
{
    try {
        DB::beginTransaction();

        // ✅ Create User
        $user = User::create([
            'prefix' => $request->prefix,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'region_id' => $request->region_id,
            'district_id' => $request->district_id,
            'tehsil_id' => $request->tehsil_id,
            'city_id' => $request->city_id,
            'center_id' => $request->center_id,
            'is_active' => $request->is_active ? 1 : 0,
        ]);

        // ✅ Assign Role
        $user->roles()->sync([$request->role_id]);

        // ✅ Attach Service (IMPORTANT FIX)
        // if ($request->center_id && $request->service_id) {

        //     $serviceCenter = ServiceCenter::where('center_id', $request->center_id)
        //         ->where('service_id', $request->service_id)
        //         ->first();

        //     if ($serviceCenter) {
        //         DB::table('service_center_users')->updateOrInsert(
        //             [
        //                 'user_id' => $user->id,
        //                 'service_center_id' => $serviceCenter->id,
        //             ],
        //             [
        //                 'created_at' => now(),
        //                 'updated_at' => now(),
        //             ]
        //         );
        //     }
        // }
        if ($request->center_id && $request->service_ids) {

    foreach ($request->service_ids as $serviceId) {

        $serviceCenter = ServiceCenter::where('center_id', $request->center_id)
            ->where('service_id', $serviceId)
            ->first();

        if ($serviceCenter) {
            DB::table('service_center_users')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'service_center_id' => $serviceCenter->id,
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

        // ✅ Create Employee
        Employee::create([
            'user_id' => $user->id,
            'cnic' => $request->cnic,
            'phone_no' => $request->phone_no,
            'address' => $request->address,
            'center_id' => $request->center_id,
            'designation_id' => $request->role_id,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        // ✅ Save Signature
        $this->saveSignFile($request, $user);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], 201);

    } catch (\Throwable $th) {
        DB::rollBack();
        Log::error($th->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Error creating user',
            'error' => $th->getMessage()
        ], 500);
    }
}

    public function show($id)
    {
        try {
            $user = User::with([
                'region:id,name,parent_id',
                'district:id,name,parent_id',
                'tehsil:id,name,parent_id',
                'center:id,name',
                'city:id,name,parent_id',
                'employee',
                'roles',
                'serviceCenters.service:id,name'
            ])->findOrFail($id);

             $services = $user->serviceCenters
            ->pluck('service')
            ->filter()
            ->unique('id')
            ->values();

        $user->services = $services;


            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'error' => $th->getMessage()
            ], 404);
        }
    }

    // Update user
    public function update(UserFormRequest $request, User $user)
    {
        try {
            DB::beginTransaction();

            $user->update([
                'prefix' => $request->prefix,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => $request->password ? bcrypt($request->password) : $user->password,
                'region_id' => $request->region_id,
                'center_id' => $request->center_id,
                'city_id' => $request->city_id,
                'district_id' => $request->district_id,
                'tehsil_id' => $request->tehsil_id,
                'is_active' => $request->is_active ? 1 : 0,
            ]);

            //Sync updated role
            $user->roles()->sync($request->role_id);

            //Update Associated Employee Record
            Employee::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'cnic' => $request->cnic,
                    'phone_no' => $request->phone_no,
                    'address' => $request->address,
                    'center_id' => $request->center_id,
                    'designation_id' => $request->role_id ?? null,
                    'updated_by' => Auth::id(),
                ]
            );

            //Save Sign file if present in request
            $this->saveSignFile($request, $user);

      if ($request->center_id && $request->service_ids) {

    // ❌ Pehle purani services delete
    DB::table('service_center_users')
        ->where('user_id', $user->id)
        ->delete();

    // ✅ New services insert
    $insertData = [];

    foreach ($request->service_ids as $serviceId) {

        $serviceCenter = ServiceCenter::where('center_id', $request->center_id)
            ->where('service_id', $serviceId)
            ->first();

        if ($serviceCenter) {
            $insertData[] = [
                'user_id' => $user->id,
                'service_center_id' => $serviceCenter->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
    }

    if (!empty($insertData)) {
        DB::table('service_center_users')->insert($insertData);
    }
}

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error updating user',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {

            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }
                
            $user->employee()->delete();


            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully!'
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting user',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    // get users
    public function getUsers()
    {
        $authUserRole = Auth::user()->roles->first()->name;

        if ($authUserRole == 'Super Admin') {
            $users = User::all();
        } elseif ($authUserRole == 'DEO') {
            $users = User::where('center_id', Auth::user()->center_id)
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'AC')
                        ->orWhere('name', 'ACR');
                })
                ->get();
        } elseif ($authUserRole == 'AC') {
            $users = User::where('center_id', Auth::user()->center_id)
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'DC');
                })
                ->get();
        } elseif ($authUserRole == 'ACR') {
            $users = User::where('center_id', Auth::user()->center_id)
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'DC');
                })
                ->get();
        }
        return response()->json($users);
    }

    public function getUserDropdownData()
    {
        return Cache::rememberForever('users-dropdown-data', function () {
            return [
                'regions' => Demography::where('type', 'REGION')
                    ->select('id', 'name', 'urdu_name', 'parent_id')
                    ->orderBy('name')
                    ->get(),
                'districts' => Demography::where('type', 'DISTRICT')
                    ->select('id', 'name', 'urdu_name', 'parent_id')
                    ->orderBy('name')
                    ->get(),
                'tehsils' => Demography::where('type', 'TEHSIL')
                    ->select('id', 'name', 'urdu_name', 'parent_id')
                    ->orderBy('name')
                    ->get(),
                    'cities' => Demography::where('type', 'CITY')
                    ->select('id', 'name', 'urdu_name', 'parent_id')
                    ->orderBy('name')
                    ->get(),
                'centers' => Center::select('id', 'name','district_id','tehsil_id')->get(),
                'roles' => Role::where('name', '!=', 'Super Admin')
                    ->select('id', 'name')
                    ->orderBy('sort_order')
                    ->get(),
                    'service_centers' => ServiceCenter::with('service:id,name')
                        ->select('id', 'center_id', 'service_id')
                        ->get()
            ];
        });
    }

    private function saveSignFile($request, $user)
    {
        //Save Sign file if present in request
        if ($request->hasFile('sign_file')) {

            // Delete old file (optional but recommended for update)
            if ($user->sign_file) {
                $oldPath = str_replace(url('/') . '/storage/', '', $user->sign_file);
                Storage::disk('public')->delete($oldPath);
            }

            $image = $request->file('sign_file');

            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();

            $user->sign_file = $image->storeAs('sign_files', $imageName, 'public');

            $user->save();
        }
    }
}
