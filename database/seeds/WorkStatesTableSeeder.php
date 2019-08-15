<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class WorkStatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dtNow = Date::now('Europe/London');
        DB::table('work_states')->insert([
            'work_state' => 'On-site',
            'created_at' => $dtNow,
            'updated_at' => $dtNow,
        ]);
        DB::table('work_states')->insert([
            'work_state' => 'Remote working',
            'created_at' => $dtNow,
            'updated_at' => $dtNow,
        ]);
        DB::table('work_states')->insert([
            'work_state' => 'Not working',
            'created_at' => $dtNow,
            'updated_at' => $dtNow,
        ]);
    }
}
