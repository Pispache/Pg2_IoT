<?php
use App\Http\Controllers\AlertController;
use App\Http\Controllers\AlertRuleController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ReadingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function(){
    Route::get('/login',[AuthController::class,'showLogin'])->name('login');
    Route::post('/login',[AuthController::class,'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function(){
    Route::post('/logout',[AuthController::class,'logout'])->name('logout');
    Route::get('/',[DashboardController::class,'index'])->name('dashboard');
    Route::get('/lecturas',[ReadingController::class,'index'])->name('readings.index');
    Route::get('/alertas',[AlertController::class,'index'])->name('alerts.index');
    Route::get('/reportes',[ReportController::class,'index'])->name('reports.index');
    Route::get('/reportes/csv',[ReportController::class,'csv'])->name('reports.csv');
    Route::get('/reportes/pdf',[ReportController::class,'pdf'])->name('reports.pdf');
    Route::get('/dispositivos/{device}',[DeviceController::class,'show'])->name('devices.show');

    Route::middleware('role:admin,supervisor')->group(function(){
        Route::get('/dispositivos',[DeviceController::class,'index'])->name('devices.index');
        Route::post('/dispositivos',[DeviceController::class,'store'])->name('devices.store');
        Route::put('/dispositivos/{device}',[DeviceController::class,'update'])->name('devices.update');
        Route::post('/dispositivos/{device}/token',[DeviceController::class,'regenerateToken'])->name('devices.token');
        Route::delete('/dispositivos/{device}',[DeviceController::class,'destroy'])->name('devices.destroy');
        Route::get('/reglas-alerta',[AlertRuleController::class,'index'])->name('alert-rules.index');
        Route::post('/reglas-alerta',[AlertRuleController::class,'store'])->name('alert-rules.store');
        Route::put('/reglas-alerta/{alertRule}',[AlertRuleController::class,'update'])->name('alert-rules.update');
        Route::delete('/reglas-alerta/{alertRule}',[AlertRuleController::class,'destroy'])->name('alert-rules.destroy');
        Route::post('/alertas/{alert}/reconocer',[AlertController::class,'acknowledge'])->name('alerts.acknowledge');
        Route::post('/alertas/{alert}/resolver',[AlertController::class,'resolve'])->name('alerts.resolve');
    });

    Route::middleware('role:admin')->group(function(){
        Route::get('/usuarios',[UserController::class,'index'])->name('users.index');
        Route::post('/usuarios',[UserController::class,'store'])->name('users.store');
        Route::put('/usuarios/{user}',[UserController::class,'update'])->name('users.update');
        Route::delete('/usuarios/{user}',[UserController::class,'destroy'])->name('users.destroy');
        Route::get('/bodegas',[WarehouseController::class,'index'])->name('warehouses.index');
        Route::post('/bodegas',[WarehouseController::class,'store'])->name('warehouses.store');
        Route::put('/bodegas/{warehouse}',[WarehouseController::class,'update'])->name('warehouses.update');
        Route::delete('/bodegas/{warehouse}',[WarehouseController::class,'destroy'])->name('warehouses.destroy');
        Route::get('/areas',[AreaController::class,'index'])->name('areas.index');
        Route::post('/areas',[AreaController::class,'store'])->name('areas.store');
        Route::put('/areas/{area}',[AreaController::class,'update'])->name('areas.update');
        Route::delete('/areas/{area}',[AreaController::class,'destroy'])->name('areas.destroy');
        Route::get('/auditoria',[AuditLogController::class,'index'])->name('audit.index');
    });
});
