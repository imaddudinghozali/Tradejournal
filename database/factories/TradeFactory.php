<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TradeFactory extends Factory
{
    public function definition(): array
    {
        $profit = fake()->randomFloat(2, -250, 400);
        return [
            'trade_date' => fake()->dateTimeBetween('-30 days','now')->format('Y-m-d'),
            'pair' => 'XAU/USD',
            'session' => fake()->randomElement(['London','New York','Asia']),
            'setup_type' => fake()->randomElement(['Breakout','Reversal','Liquidity Grab']),
            'entry_price' => fake()->randomFloat(2, 2300, 3500),
            'stop_loss' => fake()->randomFloat(2, 2300, 3500),
            'take_profit' => fake()->randomFloat(2, 2300, 3500),
            'lot_size' => fake()->randomFloat(2, 0.01, 1),
            'risk_amount' => fake()->randomFloat(2, 20, 200),
            'risk_percent' => fake()->randomFloat(2, 0.2, 2),
            'reward_amount' => fake()->randomFloat(2, 20, 400),
            'rr_ratio' => fake()->randomFloat(2, 0.5, 4),
            'result' => $profit >= 0 ? 'win' : 'loss',
            'profit_loss' => $profit,
            'amdx_phase' => fake()->randomElement(['Accumulation','Manipulation','Distribution','Expansion']),
            'liquidity_sweep' => fake()->boolean(),
            'inducement' => fake()->boolean(),
            'fvg' => fake()->boolean(),
            'order_block' => fake()->boolean(),
            'bos' => fake()->boolean(),
            'choch' => fake()->boolean(),
        ];
    }
}
