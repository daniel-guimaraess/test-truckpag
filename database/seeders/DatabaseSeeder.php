<?php

namespace Database\Seeders;

use App\Models\Alert;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'admin@backend.com.br'],
            [
            'name' => 'Admin',
            'password' => '#Admin10',
        ]);

        Alert::firstOrCreate([
            'chat_id' => null,
            'bot_token' => null
        ]);
    }
}
