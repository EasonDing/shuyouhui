<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    public function run()
    {
        $time = date("Y-m-d h:i:s");
        $data = [
            [
                "id"             => 1,
                "username"       => 'admin',
                "name"           => '管理员',
                "password"       => bcrypt('secret'),
                "status"         => 1,
                "type"           => '1',
                "sex"            => '1',
                "face"           => '/images/userLogo.png',
                "mobile"         => '15757300627',
                "poster_id"      => '',
                'group_id'       => '',
                'group_name'     => '',
                "created_at"     => $time,
                "updated_at"     => \Carbon\Carbon::now(),
            ]
        ];
        DB::table('users')->insert($data);
    }
}
