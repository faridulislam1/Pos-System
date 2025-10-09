<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Redis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis as RedisFacade; 
class RedisController extends Controller
{

    public function index(Request $request)
    {
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
    }



    public function getData()
    {
        $data = Redis::all();
        return response()->json($data);
    }

    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'des' => 'nullable|string',
            'price' => 'nullable|numeric',
        ]);

        $redis = Redis::create($validated);
        Cache::forget('redis_data');

        return response()->json($redis, 201);
    }

    public function show($id)
    {
        $data = Cache::remember("redis_data_{$id}", 60, function () use ($id) {
            return Redis::find($id);
        });

        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'des' => 'nullable|string',
            'price' => 'nullable|numeric',
        ]);

        $redis = Redis::find($id);
        if (!$redis) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $redis->update($validated);
        Cache::forget("redis_data_{$id}");
        Cache::forget('redis_data');

        return response()->json($redis);
    }

    public function destroy($id)
    {
        $redis = Redis::find($id);
        if (!$redis) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $redis->delete();
        Cache::forget("redis_data_{$id}");
        Cache::forget('redis_data');

        return response()->json(['message' => 'Deleted']);
    }
}
