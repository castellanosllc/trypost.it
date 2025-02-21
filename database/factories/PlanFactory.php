<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Premium',
            'internal_id' => Str::uuid(),
            'price' => 20,
            'is_monthly' => true,
            'stripe_id' => 'price_1QuvRZLPUlXWeZgGF6VWzYlJ',
            'access_level' => 2,
        ];
    }
}
