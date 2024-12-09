<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // 基幹ルートを追加
            Route::middleware('web')
            ->prefix('master') // 必要に応じてプレフィックスを追加
            ->group(base_path('routes/master.php'));

            // 在庫管理ルートを追加
            Route::middleware('web')
            ->prefix('stock') // 必要に応じてプレフィックスを追加
            ->group(base_path('routes/stock.php'));

            // 在庫管理ルートを追加
            Route::middleware('web')
            ->prefix('movie') // 必要に応じてプレフィックスを追加
            ->group(base_path('routes/movie.php'));
            // 在庫管理ルートを追加
            Route::middleware('web')
            ->prefix('fax') // 必要に応じてプレフィックスを追加
            ->group(base_path('routes/fax.php'));

            // タブレット用ルートを追加
            Route::middleware('web')
            ->prefix('tablet') // 必要に応じてプレフィックスを追加
            ->group(base_path('routes/tablet.php'));

            // リモート操作ルートを追加
            Route::middleware('web')
            ->prefix('remote') // 必要に応じてプレフィックスを追加
            ->group(base_path('routes/remote.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
