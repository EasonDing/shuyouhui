<?php

use Illuminate\Database\Seeder;

class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //对应 permission表 角色关联权限
        $permissions = DB::table('permissions')->get();

        $data = [];
        foreach ($permissions as $permission){
            if (substr($permission->name, 0, 5) == 'admin'){
                $data[] = [
                    'permission_id' => $permission->id,
                    "role_id"       => 1
                ];
            }else{
                $data[] = [
                    'permission_id' => $permission->id,
                    "role_id"       => 2
                ];
            }
        }
        DB::table('permission_role')->insert($data);
    }
}
