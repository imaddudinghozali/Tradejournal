<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TradingAccountFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Account ' . fake()->unique()->numerify('###'),
            'broker' => fake()->randomElement(['FTMO', 'IC Markets', 'MyFundedFX']),
            'account_number' => fake()->numerify('ACC-####'),
            'balance' => fake()->randomFloat(2, 1000, 20000),
            'equity' => fake()->randomFloat(2, 1000, 20000),
            'is_active' => true,
        ];
    }
}

