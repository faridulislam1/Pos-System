<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Product\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RedisController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis as RedisFacade; 

use App\Models\Redis;



    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('admin')->group(function () {
        Route::resource('categories', CategoryController::class);
    });


 Route::post('/registers', [AuthController::class, 'registers']);
 Route::middleware('throttle:5,1')->post('/logins', [AuthController::class, 'logins']);

//redis routes
Route::get('/products', [RedisController::class, 'index']);
Route::post('/products', [RedisController::class, 'store']);
Route::put('/products/{id}', [RedisController::class, 'update']);
Route::delete('/products/{id}', [RedisController::class, 'destroy']);
Route::get('/redisdata', [RedisController::class, 'getData']);  


Route::get('/products/check-cache', function (Request $request) {
    $perPage = 50;
        $page = (int) $request->get('page', 1);
        $cacheKey = "products_page_{$page}";
        $cached = RedisFacade::get($cacheKey);
        if ($cached) {
            return response($cached, 200)
                ->header('Content-Type', 'application/json')
                ->header('X-Cache', 'HIT');
        }
        $products = Redis::paginate($perPage);
        $formatted = [
            'per_page' => $products->perPage(),
            'to' => $products->lastItem(),
            'total' => $products->total(),
            'data' => $products->items(),
        ];
        RedisFacade::setex($cacheKey, 60, json_encode($formatted));
        return response()->json($formatted)
            ->header('X-Cache', 'MISS');
});


Route::post('/cache/clear-products', function() {
    Cache::forget('products');
    return response()->json(['message' => 'Products cache cleared']);
});

