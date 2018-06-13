<?php


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * ================================================================
 * ==========================  通用 API ============================
 * ================================================================
 */

//获取短信验证码
Route::post('/captcha', 'CaptchaController@captcha');
Route::post('/bookCodeExport', 'ExcelController@bookCodeExport');

// 获取当前登录用户的信息
Route::middleware('auth:api')->get('/admin/user', 'Admin\AdminUsersController@user');
// 导航栏菜单
Route::middleware('auth:api')->get('/system_menu', 'SystemMenuController@menu');
// 获取用户角色
Route::middleware('auth:api')->get('/role', 'RoleController@role');


//管理员 历史消息 TODO 待修改
Route::middleware('auth:api')->post('/news', 'NewsController@index');
Route::middleware('auth:api')->post('/news/{id}/destroy', 'NewsController@destroy');

//吧主管理员 历史消息 TODO 待修改
Route::middleware('auth:api')->post('/t_news', 'TNewsController@index');//获取历史消息
Route::middleware('auth:api')->post('/t_news/{id}/destroy', 'TNewsController@destroy');//获取历史消息

// TODO 待完善
Route::middleware('auth:api')->post('/t_send_sms', 'TFinancesController@sendSMS');//发送验证码
Route::middleware('auth:api')->get('/t_money', 'TMoneyOutsController@index');//申请提现
Route::middleware('auth:api')->post('/t_money_out/store', 'TMoneyOutsController@store');//申请提现

//书架管理 TODO 暂时无用
Route::middleware('auth:api')->post('/t_book', 'TBooksController@index');//获取书架信息列表
Route::middleware('auth:api')->post('/t_book/{id}/edit2', 'TBooksController@bookInfo');//上传图书编辑页面 图书信息
Route::middleware('auth:api')->post('/t_book/{id}/update', 'TBooksController@update');//更新上传图书的推荐语和价格
Route::middleware('auth:api')->post('/t_book/store', 'TBooksController@store');//添加上传图书
Route::middleware('auth:api')->post('/t_book/{id}/destroy', 'TBooksController@destroy');//删除上传图书


/**
 * ================================================================
 * ==========================  后台管理员 API =========================
 * ================================================================
 */

Route::group(['middleware' => ['auth:api', 'role:admin'], 'prefix' => '/admin'], function () {

    //首页
    Route::group(['prefix' => '/index'], function () {

        //首页统计数据
        Route::get('/count', 'Admin\IndexController@count');
        //统计每日新增用户
        Route::get('/new/user', 'Admin\IndexController@newUser');
        //统计每日新增书吧
        Route::get('/new/group', 'Admin\IndexController@newGroup');
    });

    //后台用户管理
    Route::group(['prefix' => '/admin'], function () {

        //书吧管理员列表
        Route::get('/index', 'Admin\AdminUsersController@index');
        //创建吧主账号
        Route::post('/store', 'Admin\AdminUsersController@store');
        //删除吧主账号
        Route::post('/destroy/{id}', 'Admin\AdminUsersController@destroy');
        //修改密码
        Route::post('/change/password', 'Admin\AdminUsersController@changePassword');
    });

    //App用户管理
    Route::group(['prefix' => '/user'], function () {

        //App用户列表
        Route::get('/index', 'Admin\UsersController@index');
    });


    //0元购书
    Route::group(['prefix' => 'buy/book'], function () {

        //0元购书 图书列表
        Route::get('/index', 'Admin\Admin\BuyBooksController@index');
        //0元购书 添加图书
        Route::post('/store', 'Admin\Admin\BuyBooksController@store');
        //0元购书 编辑图书
        Route::get('/edit/{id}', 'Admin\Admin\BuyBooksController@edit');
        //0元购书 更新图书
        Route::post('/update/{id}', 'Admin\Admin\BuyBooksController@update');
        //0元购书 删除图书
        Route::get('/destroy/{id}', 'Admin\Admin\BuyBooksController@destroy');
    });

    Route::group(['prefix' => 'buy/order'], function () {

        //0元购书 中奖列表
        Route::get('/index', 'Admin\Admin\BuyOrdersController@index');
    });


    //贝壳图书
    Route::group(['prefix' => '/book'], function () {

        //贝壳书架列表
        Route::get('/index', 'Admin\BooksController@index');
        //添加贝壳图书
        Route::post('/store', 'Admin\BooksController@store');
        //编辑贝壳图书
        Route::get('/edit/{id}', 'Admin\BooksController@edit');
        //更新贝壳图书
        Route::post('/update/{id}', 'Admin\BooksController@update');
        //删除贝壳图书
        Route::get('/destroy/{id}', 'Admin\BooksController@destroy');
    });

    //消息管理
    Route::group(['prefix' => '/message'], function () {

        //发送个人消息
        Route::post('store', 'Admin\MessagesController@store');
        //发送全员消息
        Route::post('all/user', 'Admin\MessagesController@allUser');
        //发送吧主消息
        Route::post('all/bar', 'Admin\MessagesController@allBar');
    });

    //订单管理
    Route::group(['prefix' => '/order'], function () {

        //订单列表
        Route::get('/index', 'Admin\OrdersController@index');
        //订单列表
        Route::get('/destroy/{id}', 'Admin\OrdersController@destroy');
    });

    //财务管理
    Route::group(['prefix' => '/finance'], function () {

        //财务列表
        Route::get('/index', 'Admin\FinancesController@index');
        //财务统计数据
        Route::get('/statistics', 'Admin\FinancesController@Statistics');
        //统计每日交易金额
        Route::get('/trading/count', 'Admin\FinancesController@tradingCount');
        //统计每日充值金额
        Route::get('/recharge/count', 'Admin\FinancesController@rechargeCount');
    });

    //书吧管理
    Route::group(['prefix' => '/group'], function () {

        //书吧列表
        Route::get('/index', 'Admin\GroupsController@index');
        //删除书吧
        Route::post('/destroy/{id}', 'Admin\GroupsController@destroy');
        //无管理员账号的书吧列表
        Route::get('/groups', 'Admin\GroupsController@groups');
    });
});


/**
 * ================================================================
 * ==========================  书吧管理员 API =======================
 * ================================================================
 */

Route::group(['middleware' => ['auth:api', 'role:bar'], 'prefix' => '/bar'], function () {

    //书吧管理员首页
    Route::group(['prefix' => '/index'], function () {

        //首页统计数据
        Route::get('/count', 'Bar\IndexController@count');
        //每日新增用户
        Route::post('/new/user', 'Bar\IndexController@newUser');
        //每日活跃用户
        Route::post('/active/user', 'Bar\IndexController@activeUser');
    });

    //书吧管理
    Route::group(['prefix' => '/group'], function () {

        //书吧详情
        Route::get('/show', 'Bar\GroupsController@show');
        //编辑书吧信息
        Route::post('/update', 'Bar\GroupsController@update');
        //书吧成员列表
        Route::post('/users', 'Bar\GroupsController@users');
        //删除书吧成员
        Route::post('/destroy/{id}/user', 'Bar\GroupsController@destroyUser');
    });

    //帖子管理
    Route::group(['prefix' => '/blog'], function () {

        //书吧帖子列表
        Route::get('/index', 'Bar\BlogsController@index');
        //删除帖子
        Route::post('/destroy/{id}', 'Bar\BlogsController@destroy');
        //书吧帖子评论列表
        Route::get('/comment/{id}', 'Bar\BlogsController@comment');
        //删除帖子评论
        Route::post('/destroy/{id}/comment', 'Bar\BlogsController@destroyComment');

    });

    //专栏管理
    Route::group(['prefix' => '/column'], function () {

        //专栏列表
        Route::get('/index', 'Bar\ColumnsController@index');
        //添加专栏
        Route::post('/store', 'Bar\ColumnsController@store');
        //专栏详情
        Route::get('/show', 'Bar\ColumnsController@show');
        //编辑专栏
        Route::get('/edit/{id}', 'Bar\ColumnsController@edit');
        //更新专栏
        Route::post('/update/{id}', 'Bar\ColumnsController@update');
        //删除专栏
        Route::get('/destroy/{id}', 'Bar\ColumnsController@destroy');
    });

    //书架管理
    Route::group(['prefix' => '/book'], function () {

        //吧书架列表
        Route::post('/index', 'Bar\BooksController@index');
        //从贝壳书库添加图书
        Route::post('/store', 'Bar\BooksController@store');
        //编辑贝壳图书
        Route::get('/edit/{id}', 'Bar\BooksController@edit');
        //更新贝壳图书
        Route::post('/update/{isbn}', 'Bar\BooksController@update');
        //移除贝壳图书
        Route::post('/destroy', 'Bar\BooksController@destroy');
        //书吧未添加的贝壳图书列表
        Route::post('/', 'Bar\BooksController@barBooks');
    });

    //Banner 管理
    Route::group(['prefix' => '/banner'], function () {

        //banner 列表
        Route::get('/index', 'Bar\BannersController@index');
        //添加 banner
        Route::post('/store', 'Bar\BannersController@store');
        //编辑 banner
        Route::get('/edit/{id}', 'Bar\BannersController@edit');
        //更新 banner
        Route::post('/update/{id}', 'Bar\BannersController@update');
        //删除 banner
        Route::get('/destroy/{id}', 'Bar\BannersController@destroy');
        //banner 上架
        Route::post('/putaway', 'Bar\BannersController@putaway');
    });

    //消息管理
    Route::group(['prefix' => '/message'], function () {

        //发送个人消息
        Route::post('store', 'Bar\MessagesController@store');
        //发送书吧全员消息
        Route::post('all/user', 'Bar\MessagesController@allUser');
    });

    //订单管理
    Route::group(['prefix' => '/order'], function () {

        //订单列表
        Route::get('/index', 'Bar\OrdersController@index');
        //删除订单
        Route::get('/destroy/{id}', 'Bar\OrdersController@destroy');
        //统计每日订单
        Route::get('/new/order', 'Bar\OrdersController@newOrder');
    });

    //财务管理
    Route::group(['prefix' => '/finance'], function () {

        //详细账单列表
        Route::get('/index', 'Bar\FinancesController@index');
        //财务统计数据
        Route::get('/count', 'Bar\FinancesController@count');
        //统计每日收入
        Route::get('/revenue', 'Bar\FinancesController@revenue');
        //统计每日支出
        Route::get('/expenditure', 'Bar\FinancesController@expenditure');
    });
});


/**
 * ================================================================
 * ========================  微信小程序 API =========================
 * ================================================================
 */
//支付回调
Route::post('/mini/program/notify', 'Api\MiniProgram\WeiXinPayController@notify');
Route::group(['prefix' => '/mini/program'], function () {

    //小程序 Auth
    Route::post('/auth', 'Api\MiniProgram\AuthsController@auth');

    Route::group(['middleware' => ['auth:miniProgram']], function () {

        //微信支付
        Route::post('/pay', 'Api\MiniProgram\WeiXinPayController@index');

        //检查用户是否关联数据
        Route::post('/checkUserInfo', 'Api\MiniProgram\AuthsController@checkUserInfo');
        //创建用户记录
        Route::post('/store', 'Api\MiniProgram\AuthsController@store');
        Route::post('/update', 'Api\MiniProgram\AuthsController@update');

        //用户信息
        Route::group(['prefix' => '/user'], function () {
            Route::post('/show', 'Api\MiniProgram\UsersController@show');
            Route::post('/bindPhone', 'Api\MiniProgram\UsersController@bindPhone');
            Route::post('/checkBindPhone', 'Api\MiniProgram\UsersController@checkBindPhone');
            Route::post('/updateBindPhone', 'Api\MiniProgram\UsersController@updateBindPhone');
            Route::post('/createOrder', 'Api\MiniProgram\UsersController@createOrder');
            Route::post('/updatePayStatus', 'Api\MiniProgram\UsersController@updatePayStatus');
        });

        Route::group(['prefix' => '/buy/book'], function () {
            //0元购书活动图书列表
            Route::post('/', 'Api\MiniProgram\BuyBooksController@index');
            //获取0元购书图书详情
            Route::get('/show/{id}', 'Api\MiniProgram\BuyBooksController@show');
        });

        Route::group(['prefix' => '/buy/order'], function () {
            Route::get('/', 'Api\MiniProgram\BuyOrdersController@index');
            //参与0元购书活动
            Route::post('/store', 'Api\MiniProgram\BuyOrdersController@store');
            Route::post('/show/{id}', 'Api\MiniProgram\BuyOrdersController@show');
            Route::post('/update/{id}', 'Api\MiniProgram\BuyOrdersController@update');
            Route::post('/payStatus/{id}', 'Api\MiniProgram\BuyOrdersController@updatePayStatus');
            Route::post('/destroy/{id}', 'Api\MiniProgram\BuyOrdersController@destroy');
        });

        Route::group(['prefix' => '/buy/invite'], function () {
            //受邀请人和与活动订单关联
            Route::post('/store', 'Api\MiniProgram\BuyInvitesController@store');
            //检测用户是否为新用户且帮助过好友
            Route::post('/checkUserInvite', 'Api\MiniProgram\BuyInvitesController@checkUserInvite');
        });
    });
});


Route::group(['prefix' => '/bookfan'], function () {

    Route::group(['prefix' => '/code'], function () {
        Route::get('/show/{code}', 'Api\BookCodesController@show');
    });

    Route::group(['prefix' => '/group'], function () {
        //TODO 书友会接口 暂时这样写
        Route::post('/isJoinGrouop', 'Api\GroupsController@isJoinGrouop');
        Route::post('/joinGroup', 'Api\GroupsController@joinGroup');
    });
});