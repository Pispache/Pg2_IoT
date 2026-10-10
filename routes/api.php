<?php
use App\Http\Controllers\Api\V1\ReadingIngestController;
use Illuminate\Support\Facades\Route;
Route::prefix('v1')->group(function(){
    Route::post('/readings',[ReadingIngestController::class,'store']);
    Route::post('/device/config',[ReadingIngestController::class,'config']);
    Route::get('/health', fn()=>response()->json(['ok'=>true,'service'=>'iot-bodega-api','time'=>now()->toIso8601String()]));
});
