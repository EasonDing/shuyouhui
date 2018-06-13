<?php

use Illuminate\Database\Seeder;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = date("Y-m-d h:i:s");

        DB::table('permissions')->insert(
            [
                //管理员
                [
                    "name" => "admin/index",
                    "display_name" => "主页",
                    "description" => "主页",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "admin/new/user",
                    "display_name" => "今日新增用户",
                    "description" => "今日新增用户",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "admin/new/group",
                    "display_name" => "今日新增书吧",
                    "description" => "今日新增书吧",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "admin/group",
                    "display_name" => "书吧列表",
                    "description" => "书吧列表",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "admin/group/admin",
                    "display_name" => "书吧管理员列表",
                    "description" => "书吧管理员列表",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "admin/book",
                    "display_name" => "贝壳书架",
                    "description" => "贝壳书架",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "admin/buy/book",
                    "display_name" => "图书列表",
                    "description" => "0元购书模块图书列表",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "admin/buy/book/add",
                    "display_name" => "添加图书",
                    "description" => "0元购书模块添加图书",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "admin/buy/book/edit",
                    "display_name" => "编辑图书",
                    "description" => "0元购书模块编辑图书",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "admin/book/add",
                    "display_name" => "添加图书",
                    "description" => "添加图书",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "admin/book/edit",
                    "display_name" => "编辑图书",
                    "description" => "编辑图书",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "admin/user",
                    "display_name" => "用户列表",
                    "description" => "用户列表",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "admin/message",
                    "display_name" => "历史消息列表",
                    "description" => "历史消息列表",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "admin/order",
                    "display_name" => "订单管理",
                    "description" => "订单管理",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "admin/finance",
                    "display_name" => "财务管理",
                    "description" => "财务管理",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "admin/recharge/money",
                    "display_name" => "今日充值金额",
                    "description" => "今日充值金额",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "admin/trading/money",
                    "display_name" => "今日交易金额",
                    "description" => "今日交易金额",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "alerts",
                    "display_name" => "通知中心",
                    "description" => "通知中心",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],

                //吧主
                [
                    "name" => "bar/index",
                    "display_name" => "主页",
                    "description" => "主页",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "bar/new/user",
                    "display_name" => "今日新增用户",
                    "description" => "今日新增用户",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "bar/active/user",
                    "display_name" => "今日活跃用户",
                    "description" => "今日活跃用户",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "bar/new/order",
                    "display_name" => "今日新增订单",
                    "description" => "今日新增订单",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "bar/group",
                    "display_name" => "书吧信息",
                    "description" => "书吧信息",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "bar/group/user",
                    "display_name" => "书吧用户列表",
                    "description" => "书吧用户列表",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "bar/column",
                    "display_name" => "专栏列表",
                    "description" => "专栏列表",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "bar/column/add",
                    "display_name" => "添加专栏",
                    "description" => "添加专栏",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "bar/column/edit",
                    "display_name" => "编辑专栏",
                    "description" => "编辑专栏",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "bar/book",
                    "display_name" => "书架管理",
                    "description" => "书架管理",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "bar/book/add",
                    "display_name" => "添加图书",
                    "description" => "添加图书",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "bar/book/edit",
                    "display_name" => "编辑图书",
                    "description" => "编辑图书",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "bar/book/select",
                    "display_name" => "选择贝壳图书",
                    "description" => "选择贝壳图书",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "bar/book/select/edit",
                    "display_name" => "编辑贝壳图书",
                    "description" => "编辑贝壳图书",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "bar/message",
                    "display_name" => "历史消息",
                    "description" => "历史消息",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "bar/order",
                    "display_name" => "订单管理",
                    "description" => "订单管理",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "bar/finance",
                    "display_name" => "财务管理",
                    "description" => "财务管理",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "bar/revenue",
                    "display_name" => "今日收入",
                    "description" => "今日收入",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "bar/expenditure",
                    "display_name" => "今日支出",
                    "description" => "今日支出",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "bar/bind/wechat",
                    "display_name" => "绑定微信",
                    "description" => "绑定微信",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "bar/money/out",
                    "display_name" => "收入提现",
                    "description" => "收入提现",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "bar/blog",
                    "display_name" => "帖子列表",
                    "description" => "帖子列表",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "bar/blog/add",
                    "display_name" => "添加帖子",
                    "description" => "添加帖子",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "bar/blog/edit",
                    "display_name" => "编辑帖子",
                    "description" => "编辑帖子",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "bar/blog/comment",
                    "display_name" => "帖子评论列表",
                    "description" => "帖子评论列表",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "bar/banner",
                    "display_name" => "banner列表",
                    "description" => "banner列表",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "bar/banner/add",
                    "display_name" => "添加banner",
                    "description" => "添加banner",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
                [
                    "name" => "bar/banner/edit",
                    "display_name" => "编辑banner",
                    "description" => "编辑banner",
                    "created_at" =>  $time,
                    'updated_at' => $time
                ],
            ]
        );
    }
}
