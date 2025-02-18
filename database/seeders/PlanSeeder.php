<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run()
    {
        // Creator Monthly
        Plan::create([
            'name' => 'Creator',
            'internal_id' => 'creator-monthly',
            'price' => 29,
            'is_monthly' => true,
            'stripe_id' => 'price_1QtrwwLPUlXWeZgGOVkHQfDd',
            'access_level' => 1,
            'is_private' => false,
            'max_accounts' => 5,
            'can_create_teams' => false,
        ]);

        // Creator Yearly
        Plan::create([
            'name' => 'Creator',
            'internal_id' => 'creator-yearly',
            'price' => 290,
            'is_monthly' => false,
            'stripe_id' => 'price_1QtrwwLPUlXWeZgG5lKXGWX0',
            'access_level' => 1,
            'is_private' => false,
            'max_accounts' => 5,
            'can_create_teams' => false,
        ]);

        // Pro Monthly
        Plan::create([
            'name' => 'Pro',
            'internal_id' => 'pro-monthly',
            'price' => 39,
            'is_monthly' => true,
            'stripe_id' => 'price_1QtrxOLPUlXWeZgGtdCuQVNd',
            'access_level' => 2,
            'is_private' => false,
            'max_accounts' => 10,
            'can_create_teams' => true,
        ]);

        // Pro Yearly
        Plan::create([
            'name' => 'Pro',
            'internal_id' => 'pro-yearly',
            'price' => 390,
            'is_monthly' => false,
            'stripe_id' => 'price_1QtrxOLPUlXWeZgG4ieMvafn',
            'access_level' => 2,
            'is_private' => false,
            'max_accounts' => 10,
            'can_create_teams' => true,
        ]);

        // Growth Monthly
        Plan::create([
            'name' => 'Growth',
            'internal_id' => 'growth-monthly',
            'price' => 49,
            'is_monthly' => true,
            'stripe_id' => 'price_1QtrxfLPUlXWeZgGwyTH9ZG1',
            'access_level' => 3,
            'is_private' => false,
            'max_accounts' => 30,
            'can_create_teams' => true,
        ]);

        // Growth Yearly
        Plan::create([
            'name' => 'Growth',
            'internal_id' => 'growth-yearly',
            'price' => 490,
            'is_monthly' => false,
            'stripe_id' => 'price_1QtrxsLPUlXWeZgGedpZBvvq',
            'access_level' => 3,
            'is_private' => false,
            'max_accounts' => 30,
            'can_create_teams' => true,
        ]);

        // Agency Monthly
        Plan::create([
            'name' => 'Agency',
            'internal_id' => 'agency-monthly',
            'price' => 99,
            'is_monthly' => true,
            'stripe_id' => 'price_1QtryTLPUlXWeZgGub7WPJ03',
            'access_level' => 4,
            'is_private' => false,
            'max_accounts' => 100,
            'can_create_teams' => true,
        ]);

        // Agency Yearly
        Plan::create([
            'name' => 'Agency',
            'internal_id' => 'agency-yearly',
            'price' => 990,
            'is_monthly' => false,
            'stripe_id' => 'price_1QtryTLPUlXWeZgGMNBdgXAy',
            'access_level' => 4,
            'is_private' => false,
            'max_accounts' => 100,
            'can_create_teams' => true,
        ]);
    }
}
