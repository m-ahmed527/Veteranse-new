<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Grooming Services'],
            ['name' => 'Hygiene Services'],
            ['name' => 'Specialized Services'],

        ];
        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
