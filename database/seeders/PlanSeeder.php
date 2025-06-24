<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plan::create([
            'name' => 'Free Plan',
            'slug' => 'free-plan',
            'description' => 'This is a free plan with 7.5% tax.',
            'price_id' => 'price_1RdF7zH9478kNO1Sc84on1Ld',
            'price' => 0.00,
            'currency' => 'USD',
            'is_active' => true,
        ]);

        Plan::create([
            'name' => 'Titanium Plan',
            'slug' => 'titanium-plan',
            'description' => 'This is a Titanium plan no tax.',
            'price_id' => 'price_1RdF8ZH9478kNO1SH0Frfy9O',
            'price' => 50.00,
            'currency' => 'USD',
            'is_active' => true,
        ]);
    }
}
