<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all students
        $students = DB::table('students')->get();

        foreach ($students as $student) {
            // Check if user already exists for this student
            $userExists = DB::table('users')->where('student_id', $student->id)->exists();

            if (!$userExists) {
                // Generate a unique email based on NISN
                $email = $student->nisn . '@sakti.sch.id';
                
                // Ensure email is unique in users table
                $emailExists = DB::table('users')->where('email', $email)->exists();
                if ($emailExists) {
                    $email = $student->nisn . '_' . time() . '@sakti.sch.id';
                }

                // Insert student user account
                // Default password is their NISN
                DB::table('users')->insert([
                    'name' => $student->name,
                    'email' => $email,
                    'password' => Hash::make($student->nisn),
                    'role' => 'student',
                    'student_id' => $student->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete users with role student
        DB::table('users')->where('role', 'student')->delete();
    }
};
