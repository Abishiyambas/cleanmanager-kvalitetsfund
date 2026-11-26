<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['Sara Supervisor', 'sara@demo.dk', 'supervisor'],
            ['Søren Verifier', 'soeren@demo.dk', 'supervisor'],
            ['Ida Inspektør', 'ida@demo.dk', 'inspector'],
            ['Mads Medarbejder', 'mads@demo.dk', 'employee'],
            ['Lene Medarbejder', 'lene@demo.dk', 'employee'],
        ];

        foreach ($users as [$name, $email, $role]) {
            User::factory()->create(['name' => $name, 'email' => $email, 'role' => $role]);
        }
    }
}
