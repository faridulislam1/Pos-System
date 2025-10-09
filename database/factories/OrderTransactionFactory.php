<?php

namespace Database\Factories;

use App\Models\OrderTransaction;
use App\Models\Order;
use App\Models\Customer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderTransactionFactory extends Factory
{
  protected $model = OrderTransaction::class;

  public function definition()
  {
    $year = now()->year; 
    $month = $this->faker->numberBetween(1, 12); 
    $createdAt = Carbon::create($year, $month, rand(1, 28), rand(0, 23), rand(0, 59), rand(0, 59));

    return [
      'amount' => $this->faker->randomFloat(2, 10, 1000),
      'order_id' => Order::inRandomOrder()->first()->id ?? Order::factory(),
      'customer_id' => Customer::inRandomOrder()->first()->id ?? Customer::factory(),
      'user_id' => rand(0, 1) ? (User::inRandomOrder()->first()->id ?? User::factory()) : null,
      'paid_by' => $this->faker->randomElement(['cash', 'card', 'bank']),
      'transaction_id' => $this->faker->optional()->uuid,
      'created_at' => $createdAt, 
      'updated_at' => $createdAt,
    ];
  }
}
