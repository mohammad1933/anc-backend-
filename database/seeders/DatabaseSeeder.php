<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(['email' => 'm@m.com'], [
            'name' => 'Administrator',
            'password' => 'mohamad1950',
            'role' => 'admin',
        ]);

        $this->call(ProjectSeeder::class);
        $this->call(FavoriteSeeder::class);
    }
}
