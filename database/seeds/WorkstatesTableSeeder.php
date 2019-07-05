<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class WorkstatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dtNow = Date::now('Europe/London');
        DB::table('workstates')->insert([
            'workstate' => 'On-site',
            'created_at' => $dtNow,
            'updated_at' => $dtNow,
        ]);
        DB::table('workstates')->insert([
            'workstate' => 'Remote working',
            'created_at' => $dtNow,
            'updated_at' => $dtNow,
        ]);
        DB::table('workstates')->insert([
            'workstate' => 'Not working',
            'created_at' => $dtNow,
            'updated_at' => $dtNow,
        ]);
    }
}
