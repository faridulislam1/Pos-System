<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Redis;

class RedisTableSeeder extends Seeder
{
     public function run()
    {
        for ($i = 0; $i < 100; $i++) {
            Redis::factory()->count(1000)->create();
        }
    }
}
