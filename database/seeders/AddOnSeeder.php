<?php

namespace Database\Seeders;

use App\Models\AddOn;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddOnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $addons = [
            ['name' => 'Extra Time'],
            ['name' => 'Premium Products'],
            ['name' => 'Weekend Availability'],
            ['name' => 'Senior Staff Only'],
        ];
        foreach ($addons as $addon) {
            AddOn::create($addon);
        }
    }
}
