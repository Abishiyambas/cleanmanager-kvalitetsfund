<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Glas og vinduer', 'Sanitet og forbrugsvarer', 'Gulve', 'Affald', 'Overflader og inventar'] as $name) {
            Category::create(['name' => $name]);
        }
    }
}
