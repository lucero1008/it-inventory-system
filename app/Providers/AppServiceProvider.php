<?php

namespace App\Providers;

use App\Models\Assets;
use App\Models\DesktopLaptopDetails;
use App\Models\User;
use App\Observers\AssetsObserver;
use App\Observers\TestObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        Assets::observe(AssetsObserver::class);
    }
}
