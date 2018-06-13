<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Yaml\Yaml;

class SystemMenuController extends Controller
{
    public function menu()
    {
        $user = Auth::user();
        //获取用户角色
        $user = User::with('roles')->find($user->id);

        $role = collect($user->roles)->first();

        //获取模块配置文件 进行解析获取菜单列表
        $yamlPath = base_path('config/menu/' . $role->name . '/configuration.yaml');
        $yaml = Yaml::parseFile($yamlPath);
        $menu = Arr::get($yaml, 'menus.finance.children');


        return $this->sendResponse($menu, '请求成功!');
    }

}
