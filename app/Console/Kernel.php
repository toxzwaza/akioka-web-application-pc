<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // 毎日午前9時に実行
        $schedule->command('your:command')->dailyAt('09:00');
        
        // 毎時実行
        $schedule->command('your:command')->hourly();
        
        // 特定の時間に実行（例：毎日13:30）
        $schedule->command('your:command')->dailyAt('13:30');
        
        // 特定の曜日の特定の時間に実行（例：毎週月曜日の10:00）
        $schedule->command('your:command')->weeklyOn(1, '10:00');
        
        // カスタムスケジュール（例：毎日9:00と17:00）
        $schedule->command('your:command')
            ->twiceDaily(9, 17);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
