<?php

namespace Database\Seeders;

use App\Models\Company\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeederCypress extends Seeder
{
    public function run(): void
    {
         $user = User::factory()->create([
             'email' => 'test@devqaly.com',
             'password' => Hash::make('password'),
         ]);

         Company::factory()->create([
             'created_by_id' => $user->id,
         ]);
    }
}
