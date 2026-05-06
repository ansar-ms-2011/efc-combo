<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DuplicateReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parent = ['name' => 'duplicate_reasons', 'urdu_name'=>'چوری شدہ/گم شدہ'];
        $reasons = [
            ['name' => 'Lost/Stolen', 'urdu_name'=>'چوری شدہ/گم شدہ'],
            ['name' => 'Faded out', 'urdu_name'=>'دھندلا'],
            ['name' => 'Name Change After Marriage', 'urdu_name'=>'شادی کے بعد نام کی تبدیلی'],
        ];
        $parent = Type::create($parent);
        foreach ($reasons as $reason){
            Type::create(array_merge($reason,['parent_id'=>$parent->id]));
        }
    }
}
