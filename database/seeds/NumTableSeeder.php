<?php

use Illuminate\Database\Seeder;

class NumTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //存储数字0-9为了方便生成日期表
        DB::table('num')->insert([
            [
                "id"  =>1,
                "i"   => 0,
            ],
            [
                "id"  =>2,
                "i"   => 1,
            ],
            [
                "id"  =>3,
                "i"   => 2,
            ],
            [
                "id"  =>4,
                "i"   => 3,
            ],
            [
                "id"  =>5,
                "i"   => 4,
            ],
            [
                "id"  =>6,
                "i"   => 5,
            ],
            [
                "id"  =>7,
                "i"   => 6,
            ],
            [
                "id"  =>8,
                "i"   => 7,
            ],
            [
                "id"  =>9,
                "i"   => 8,
            ],
            [
                "id"  =>10,
                "i"   => 9,
            ]
        ]);
    }
}
