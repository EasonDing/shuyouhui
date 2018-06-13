<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CalendarTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //生成一张日期表
        DB::statement('
            INSERT INTO calendars(`updateTime`) SELECT  
        adddate(  
            (   -- 这里的起始日期，你可以换成当前日期  
                DATE_FORMAT("2016-1-1", \'%Y-%m-%d\')   
            ),  
            numlist.id  
        ) AS `date`  
    FROM  
        (  
            SELECT  
                n1.i + n10.i * 10 + n100.i * 100 + n1000.i * 1000 AS id  
            FROM  
                num n1  -- num指另一张表
            CROSS JOIN num AS n10  
            CROSS JOIN num AS n100  
            CROSS JOIN num AS n1000  
        ) AS numlist;
        ');
    }
}
