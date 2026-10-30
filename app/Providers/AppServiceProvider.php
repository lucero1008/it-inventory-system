<?php

namespace App\Providers;

use App\Models\Device;
use App\Models\Monitor;
use App\Models\Printer;
use App\Models\User;
use App\Observers\DeviceObserver;
use App\Observers\MonitorObserver;
use App\Observers\PrinterObserver;
use Illuminate\Support\ServiceProvider;
use App\Observers\UserObserver;

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
        Device::observe(DeviceObserver::class);
        Monitor::observe(MonitorObserver::class);
        Printer::observe(PrinterObserver::class);
    }
}
