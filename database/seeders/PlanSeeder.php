<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run()
    {
        // Pro
        Plan::create([
            'name' => 'Pro',
            'internal_id' => 'pro-monthly',
            'price' => 49,
            'is_monthly' => true,
            'stripe_id' => 'price_1Qj8IPDyFIFKS9tcDlxQDtbV',
            'access_level' => 2,
            'is_private' => false,
            'is_archived' => false
        ]);

        // Scale
        Plan::create([
            'name' => 'Scale',
            'internal_id' => 'scale-monthly',
            'price' => 99,
            'is_monthly' => true,
            'stripe_id' => 'price_1Qj8IBDyFIFKS9tcru7wwBTR',
            'access_level' => 3,
            'is_private' => false,
            'is_archived' => false
        ]);

        /**
         * Addons
         */

        // Seats
        Plan::create([
            'name' => 'Extra Seats',
            'internal_id' => 'extra-seats',
            'price' => 20,
            'is_monthly' => true,
            'stripe_id' => 'price_1Qj8IsDyFIFKS9tclsD805cj',
            'access_level' => 0,
            'is_private' => false,
            'is_archived' => false
        ]);

        // Contacts
        Plan::create([
            'name' => 'Extra Contacts',
            'internal_id' => 'extra-contacts',
            'price' => 20,
            'is_monthly' => true,
            'stripe_id' => 'price_1Qj8JTDyFIFKS9tc9xig040x',
            'access_level' => 0,
            'is_private' => false,
            'is_archived' => false
        ]);

        // Helpdesk
        Plan::create([
            'name' => 'Extra Helpdesk',
            'internal_id' => 'extra-helpdesk',
            'price' => 50,
            'is_monthly' => true,
            'stripe_id' => 'price_1Qj8JjDyFIFKS9tciK8E8p3k',
            'access_level' => 0,
            'is_private' => false,
            'is_archived' => false
        ]);

        // News Center
        Plan::create([
            'name' => 'Extra News Center',
            'internal_id' => 'extra-news-center',
            'price' => 50,
            'is_monthly' => true,
            'stripe_id' => 'price_1Qj8K5DyFIFKS9tcHHFrcRXR',
            'access_level' => 0,
            'is_private' => false,
            'is_archived' => false
        ]);
    }
}
