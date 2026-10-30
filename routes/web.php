<?php
// use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceListController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\MonitorListController;
use App\Http\Controllers\PrinterListController;
use App\Http\Controllers\UserController;

Route::middleware(['guest', 'prevent-back-history'])->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});


Route::middleware(['auth', 'prevent-back-history'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'showDashboard'])->name('dashboard');

    // Device Routing Start
    Route::get('/device', [DeviceListController::class, 'showDevice'])->name('device.index');
    Route::get('/device/action', [DeviceListController::class, 'action'])->name('device.add');
    Route::get('/device/action/{id}', [DeviceListController::class, 'action'])->name('device.edit');
    Route::get('/device/view/{id}', [DeviceListController::class, 'action'])->name('device.view');
    Route::post('/device/store', [DeviceListController::class, 'store'])->name('device.store');
    Route::put('/device/update/{device}', [DeviceListController::class, 'update'])->name('device.update');
    Route::delete('/device/delete/{id}', [DeviceListController::class, 'destroy'])->name('device.delete');
    // Device Routing End

    // Monitor Routing Start
    Route::get('/monitor', [MonitorListController::class, 'showMonitor'])->name('monitor.index');
    Route::get('/monitor/action', [MonitorListController::class, 'action'])->name('monitor.add');
    Route::get('/monitor/action/{id}', [MonitorListController::class, 'action'])->name('monitor.edit');
    Route::get('/monitor/view/{id}', [MonitorListController::class, 'action'])->name('monitor.view');
    Route::post('/monitor/store', [MonitorListController::class, 'store'])->name('monitor.store');
    Route::put('/monitor/update/{monitor}', [MonitorListController::class, 'update'])->name('monitor.update');
    Route::delete('/monitor/delete/{id}', [MonitorListController::class, 'destroy'])->name('monitor.delete');
    // Monitor Routing End

    // Printer Routing Start
    Route::get('/printer', [PrinterListController::class, 'showPrinter'])->name('printer.index');
    Route::get('/printer/action', [PrinterListController::class, 'action'])->name('printer.add');
    Route::get('/printer/action/{id}', [PrinterListController::class, 'action'])->name('printer.edit');
    Route::get('/printer/view/{id}', [PrinterListController::class, 'action'])->name('printer.view');
    Route::post('/printer/store', [PrinterListController::class, 'store'])->name('printer.store');
    Route::put('/printer/update/{printer}', [PrinterListController::class, 'update'])->name('printer.update');
    Route::delete('/printer/delete/{id}', [PrinterListController::class, 'destroy'])->name('printer.delete');
    // Printer Routing End
});

Route::middleware(['auth', 'prevent-back-history', 'admin'])->group(function () {
    // User Routing Start
    Route::get('/users', [UserController::class, 'showUser'])->name('users.index');
    Route::get('/users/action', [UserController::class, 'action'])->name('users.add');
    Route::get('/users/action/{id}', [UserController::class, 'action'])->name('users.edit');
    Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/update/{user}', [UserController::class, 'update'])->name('user.update');
    // User Routing End

    // Activity Logs Routing Start
    Route::get('/logs', [LogsController::class, 'showLogs'])->name('logs.index');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
