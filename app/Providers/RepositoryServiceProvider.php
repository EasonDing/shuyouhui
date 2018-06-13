<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\App\Repositories\Bar\UserRepository::class, \App\Repositories\Bar\UserRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\Bar\GroupRepository::class, \App\Repositories\Bar\GroupRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\Bar\BlogRepository::class, \App\Repositories\Bar\BlogRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\Bar\BlogCommentRepository::class, \App\Repositories\Bar\BlogCommentRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\Bar\ColumnRepository::class, \App\Repositories\Bar\ColumnRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\Bar\ColumnCommentRepository::class, \App\Repositories\Bar\ColumnCommentRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\Bar\BookRepository::class, \App\Repositories\Bar\BookRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\Admin\BookRepository::class, \App\Repositories\Admin\BookRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\Bar\BannerRepository::class, \App\Repositories\Bar\BannerRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\Bar\MessageRepository::class, \App\Repositories\Bar\MessageRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\Admin\UserRepository::class, \App\Repositories\Admin\UserRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\Admin\MessageRepository::class, \App\Repositories\Admin\MessageRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\Admin\GroupRepository::class, \App\Repositories\Admin\GroupRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\Admin\AdminUserRepository::class, \App\Repositories\Admin\AdminUserRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\Admin\OrderRepository::class, \App\Repositories\Admin\OrderRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\Bar\OrderRepository::class, \App\Repositories\Bar\OrderRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\Admin\FinanceRepository::class, \App\Repositories\Admin\FinanceRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\Bar\FinanceRepository::class, \App\Repositories\Bar\FinanceRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\Admin\Admin\BuyBookRepository::class, \App\Repositories\Admin\Admin\BuyBookRepositoryEloquent::class);

        $this->app->bind(\App\Repositories\Api\MiniProgram\BuyInviteRepository::class, \App\Repositories\Api\MiniProgram\BuyInviteRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\Api\MiniProgram\BuyBookRepository::class, \App\Repositories\Api\MiniProgram\BuyBookRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\Api\MiniProgram\BuyOrderRepository::class, \App\Repositories\Api\MiniProgram\BuyOrderRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\Api\MiniProgram\UserRepository::class, \App\Repositories\Api\MiniProgram\UserRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\Api\MiniProgram\WeiXinUserRepository::class, \App\Repositories\Api\MiniProgram\WeiXinUserRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\Api\OfficialAccount\AuthRepository::class, \App\Repositories\Api\OfficialAccount\AuthRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\Api\OfficialAccount\LoginRepository::class, \App\Repositories\Api\OfficialAccount\LoginRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\Api\GroupRepository::class, \App\Repositories\Api\GroupRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\Api\BookCodeRepository::class, \App\Repositories\Api\BookCodeRepositoryEloquent::class);
        //:end-bindings:
    }
}
