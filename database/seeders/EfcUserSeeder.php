<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EfcUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::connection('mysql_efc')->table('users')->orderBy('id')->chunk(10, function ($users) {

            foreach ($users as $user) {

                $role_efc = DB::connection('mysql_efc')->table('role_type')->find($user->role_type_id);
                // $region_efc = DB::connection('mysql_efc')->table('region')->find($users->region_id);
                $district_efc = DB::connection('mysql_efc')->table('district')->find($user->district_id);
                $tehsil_efc = DB::connection('mysql_efc')->table('city')->find($user->tehsil_id);
                $center_efc = DB::connection('mysql_efc')->table('center')->find($user->center_id);
                $department_efc = DB::connection('mysql_efc')->table('department')->find($user->department_id);

                $district = $district_efc? DB::connection('mysql')->table('demographies')
                    ->where('name', $district_efc->name)
                    ->where('type', '=', 'DISTRICT')->first(): null;

                $tehsil = $tehsil_efc? DB::connection('mysql')->table('demographies')
                    ->where('name', $tehsil_efc->name)
                    ->where('type', '=', 'TEHSIL')->first() : null;

                $center = $center_efc? DB::connection('mysql')->table('centers')
                    ->where('name', $center_efc->name)->first() : null;

                $department = $department_efc? DB::connection('mysql')->table('departments')
                    ->where('name', '=', $department_efc->name)->first() : null;

                $role = $role_efc ? DB::connection('mysql')->table('roles')
                    ->where('name', '=', $role_efc->role==='Center Admin'? 'Center In-charge': $role_efc->role)
                    ->first() : null;


                $id = DB::connection('mysql')->table('users')->insertGetId([
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email ?? $user->id.'@efc.com',
                    'password' => Hash::make('cdigital99'),
                    'is_active' => $user->status === 'Active' ? 1 : 0,
                    'e_sign' => $user->esign,
                    'prefix' => $user->pre,
                    'department_id' => optional($department)->id ?? null,
                    'center_id' => optional($center)->id ?? null,
                    'tehsil_id' => optional($tehsil)->id ?? null,
                    'district_id' => optional($district)->id ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                DB::connection('mysql')->table('employees')->insert([
                    'user_id' => $id,
                    'cnic' => $user->cnic,
                    'address' => $user->address,
                    'phone_no' => $user->phone_no,
                ]);

                if($role){
                    DB::connection('mysql')->table('model_has_roles')->insert([
                        'model_id' => $id,
                        'model_type' => 'App\Models\User',
                        'role_id' => optional($role)->id ?? null,
                    ]);
                }
            }
        });
    }
}
