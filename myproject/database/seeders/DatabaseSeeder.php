<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        DB::table('roles')->insert([
            'name' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('roles')->insert([
            'name' => 'game_publisher',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('roles')->insert([
            'name' => 'player',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('game_genres')->insert([
            'name' => 'Action',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('game_genres')->insert([
            'name' => 'FPS',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('game_genres')->insert([
            'name' => 'RPG',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('game_genres')->insert([
            'name' => 'Sport',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('game_genres')->insert([
            'name' => 'Puzzle',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

    }
}
