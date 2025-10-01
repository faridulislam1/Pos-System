<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Redis;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Redis>
 */
class RedisFactory extends Factory
{
    protected $model = Redis::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'des' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(100, 10000),
        ];
    }
}
