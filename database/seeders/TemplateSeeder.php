<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Template;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    public function run(): void
    {
        $cat = Category::pluck('id', 'name');

        $templates = [
            'Daglig kontrol – indgang og toiletter' => [
                ['Indgangsglas og glasdøre er fri for fingeraftryk og snavs', 'Glas og vinduer'],
                ['Sæbedispensere er fyldt', 'Sanitet og forbrugsvarer'],
                ['Papirhåndklæder og toiletpapir er fyldt op', 'Sanitet og forbrugsvarer'],
                ['Toiletter og håndvaske er rengjorte', 'Sanitet og forbrugsvarer'],
                ['Gulve er fejet og vasket', 'Gulve'],
                ['Skraldespande er tømt', 'Affald'],
            ],
            'Ugentlig kontrol – kantine' => [
                ['Borde og stole er aftørret', 'Overflader og inventar'],
                ['Gulv under borde er rengjort', 'Gulve'],
                ['Affaldssortering er tømt og korrekt', 'Affald'],
                ['Vinduer og vindueskarme er rene', 'Glas og vinduer'],
            ],
        ];

        foreach ($templates as $name => $items) {
            $template = Template::create(['name' => $name]);
            foreach ($items as $i => [$text, $category]) {
                $template->items()->create(['text' => $text, 'category_id' => $cat[$category], 'position' => $i]);
            }
        }
    }
}
