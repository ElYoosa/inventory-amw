<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use App\Models\InTransaction;
use App\Models\OutTransaction;
use App\Observers\TransactionObserver;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\EnsureRoleIsAdmin;
use App\Http\Middleware\EnsureRoleIsManager;
use App\Http\Middleware\EnsureRoleIsStaff;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Router $router): void
    {
        // Daftarkan middleware kustom
        $router->aliasMiddleware('role', RoleMiddleware::class);
        $router->aliasMiddleware('admin', EnsureRoleIsAdmin::class);
        $router->aliasMiddleware('manager', EnsureRoleIsManager::class);
        $router->aliasMiddleware('staff', EnsureRoleIsStaff::class);

        // Daftarkan observer transaksi
        InTransaction::observe(TransactionObserver::class);
        OutTransaction::observe(TransactionObserver::class);
    }
}
