<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $number = 0;
        $data = [];
        for ($i = 0; $i < 100; $i++) {
            $http = 'http://na.bookfan.cn/api/bookfan/code/show/';
            $randomString = str_random(30);
            $number++;
            $data[] = [
                'code'            => $http . $randomString,
                'activity_status' => 0,
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ];
            if ($number == 200) {
                DB::table('book_codes')->insert($data);
                $number = 0;
                $data = [];
            }
        }

        DB::table('book_codes')->insert($data);

    }
}
