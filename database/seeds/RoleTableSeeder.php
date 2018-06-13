<?php

use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = date("Y-m-d h:i:s");

        DB::table('roles')->insert(
          [
              [
                  "name" => "admin",
                  "display_name" => "管理员",
                  "description" => "后台的管理员",
                  "created_at" =>  $time,
                  'updated_at' => $time
              ],
              [
                  "name" => "bar",
                  "display_name" => "书吧管理员",
                  "description" => "书吧管理员",
                  "created_at" =>  $time,
                  'updated_at' => $time
              ]
          ]
        );
    }
}
