<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Redis;
use Illuminate\Support\Facades\Cache;
class RedisController extends Controller
{
    public function index()
    {
        $data = Cache::remember('redis_data', 60, function () {
            return Redis::all();
        });

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
