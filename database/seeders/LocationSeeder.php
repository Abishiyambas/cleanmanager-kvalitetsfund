<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['Hovedindgang', 'Bygning A, stueetage', 'Glasdøre, måtter og reception.'],
            ['Toiletter 1. sal', 'Bygning A, 1. sal', 'Herre-, dame- og handicaptoilet.'],
            ['Kantine', 'Bygning B, stueetage', 'Spiseområde og opvaskzone.'],
            ['Mødelokale Fyn', 'Bygning A, 2. sal', 'Mødelokale til 12 personer.'],
        ];

        foreach ($locations as [$name, $address, $description]) {
            Location::create(compact('name', 'address', 'description'));
        }
    }
}
