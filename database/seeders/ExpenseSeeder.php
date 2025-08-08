<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Expense;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 5 test users
        $users = User::factory(5)->create();

        // Create expenses for each user
        $users->each(function ($user) {
            // Create 15-25 random expenses per user
            Expense::factory()
                ->count(rand(15, 25))
                ->create([
                    'user_id' => $user->id
                ]);
        });

        // Create a specific test user with known credentials
        $testUser = User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        // Create expenses for the test user
        Expense::factory()
            ->count(30)
            ->create([
                'user_id' => $testUser->id
            ]);
    }
}