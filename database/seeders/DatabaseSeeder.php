<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create(
            [
                'email' => 'admin@xmlshop.com',
                'password' => Hash::make('F4&rf;J9$Nwawxg6'),
            ]
        );
    }
}
