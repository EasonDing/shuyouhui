<?php

namespace App\Console;

use App\Entities\BuyBook;
use App\Entities\BuyOrder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->call(function () {
            $buyOrders = BuyOrder::query()->where('activity_status', 0)->with('buyInvite')->get();
            if ($buyOrders) {
                foreach ($buyOrders as $buyOrder) {
                    $isTime = (strtotime($buyOrder->created_at) + 24 * 60 * 60) - time();
                    if ($isTime <= 0) {
                        $invites = $buyOrder->buyInvite;
                        //邀请人总数
                        $inviteCount = 0;
                        foreach ($invites as $invite) {
                            //24小时内有效的邀请人
                            if (strtotime($invite->created_at) < strtotime($buyOrder->created_at) + 24 * 60 * 60) {
                                $inviteCount++;
                            }
                        }

                        //邀请人数达到12人 就将书本送出
                        $bookId = $buyOrder->book_id;
                        $orderId = $buyOrder->id;
                        if ($inviteCount >= 12) {
                            DB::beginTransaction();
                            $buyOrder = BuyOrder::query()->where('id', $orderId)->update([
                                'activity_status' => 2
                            ]);
                            $buyBook = BuyBook::query()->where('id', $bookId)->update([
                                'activity_status' => 2
                            ]);
                            if ($buyOrder && $buyBook) {
                                DB::commit();
                            } else {
                                DB::rollback();
                            }
                        } else {
                            DB::beginTransaction();
                            $buyOrder = BuyOrder::query()->where('id', $orderId)->update([
                                'activity_status' => 1
                            ]);
                            $buyBook = BuyBook::query()->where('id', $bookId)->update([
                                'activity_status' => 0
                            ]);
                            if ($buyOrder && $buyBook) {
                                DB::commit();
                            } else {
                                DB::rollback();
                            }
                        }
                    }
                }
            }
        })->hourly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
