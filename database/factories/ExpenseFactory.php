<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'Food & Dining',
            'Transportation',
            'Shopping',
            'Entertainment',
            'Bills & Utilities',
            'Healthcare',
            'Travel',
            'Education',
            'Personal Care',
            'Home & Garden',
            'Groceries',
            'Gas & Fuel',
            'Insurance',
            'Subscriptions',
            'Gifts & Donations'
        ];

        return [
            'user_id' => User::factory(),
            'date' => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'category' => $this->faker->randomElement($categories),
            'description' => $this->faker->sentence(3),
            'amount' => $this->faker->randomFloat(2, 5, 500), // Between $5 and $500
        ];
    }
}