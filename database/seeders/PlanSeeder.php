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
            'name' => 'Free',
            'internal_id' => 'free',
            'price' => 0,
            'is_monthly' => true,
            'stripe_id' => null,
            'access_level' => 1,
        ]);

        // Premium Monthly
        Plan::create([
            'name' => 'Premium',
            'internal_id' => 'premium-monthly',
            'price' => 20,
            'is_monthly' => true,
            'stripe_id' => 'price_1QuvRZLPUlXWeZgGF6VWzYlJ',
            'access_level' => 2,
        ]);

        // Premium Yearly
        Plan::create([
            'name' => 'Premium',
            'internal_id' => 'premium-yearly',
            'price' => 192,
            'is_monthly' => false,
            'stripe_id' => 'price_1QuvRZLPUlXWeZgGZ6nApWmw',
            'access_level' => 2,
        ]);
    }
}
