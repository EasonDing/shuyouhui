<?php

namespace App\Http\Controllers;

use App\EmptyData;
use App\Menu;
use App\Permission;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('perms')->get();

        return $this->sendResponse($roles, '');
    }

    public function permission(Request $request)
    {
        $role_id = $request->get('role');
        $menu_ids = $request->get('menus');

        $menus = Menu::whereIn('id', $menu_ids)->get();
        $permissions = Permission::where('name', 'like', 'menu%')->get();

        $perm_array = [];

        foreach ($menus as $item) {
            if($item->url != ''){
                $permStr = str_replace('/', 'menu-', $item->url);
                $perm = $permissions->where('name', $permStr)->first();

                if($perm){
                    $perm_array = array_merge($perm_array, [$perm->id]);
                }
            }
        }

        $role = Role::find($role_id);

        if($role){
            $role->perms()->sync($perm_array);
            return $this->sendResponse(new EmptyData(), '权限设置成功');
        }else {
            return $this->sendError(new EmptyData(), '权限设置失败');
        }
    }

    /*
     * 获取用户角色
     * @param role admin|bar-admin
     */
    public function role()
    {
        $user = Auth::user();
        $user = User::with('roles')->find($user->id);

        $role = collect($user->roles)->first();

        return $this->sendResponse($role, '用户角色获取成功!');

    }
}
