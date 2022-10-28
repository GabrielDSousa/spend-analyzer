<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'date' => fake()->dateTimeThisDecade(),
            'amount' => fake()->randomFloat(2, -1000, 1000),
            'description' => fake()->sentence(4),
            'file' => null,
            'bank' => fake()->company(), // password
            'type' => fake()->randomElement(['credit', 'debit'])
        ];
    }
}
