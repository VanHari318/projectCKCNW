<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\QuizSeeder;
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
        // Create demo teacher account
        User::factory()->create([
            'name' => 'Thầy Giáo',
            'email' => 'teacher@example.com',
            'password' => 'password',
            'role' => 'teacher',
        ]);

        // Create demo student account
        User::factory()->create([
            'name' => 'Học Sinh',
            'email' => 'student@example.com',
            'password' => 'password',
            'role' => 'student',
        ]);
    }
}
