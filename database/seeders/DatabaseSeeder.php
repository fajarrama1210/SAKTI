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
        \App\Models\User::forceCreate([
            'name' => 'admin',
            'email' => 'asd@asd.asd',
            'password' => bcrypt('asdasd'),
            'role' => 'admin',
            'email_verified_at' => now(),
            'remember_token' => \Illuminate\Support\Str::random(10),
        ]);
        \App\Models\User::factory()->create([
            'name' => 'mulyono',
            'email' => 'E3125mulyono@ac.id',
            'password' => bcrypt('mulyono'),
            'role' => 'admin',
            'email_verified_at' => now(),
            'remember_token' => \Illuminate\Support\Str::random(10),
        ]);
        \App\Models\User::forceCreate([
            'name' => 'Kepala Sekolah SAKTI',
            'email' => 'kepala@sekolah.id',
            'password' => bcrypt('kepala123'),
            'role' => 'kepala_sekolah',
            'email_verified_at' => now(),
            'remember_token' => \Illuminate\Support\Str::random(10),
        ]);
    }
}
