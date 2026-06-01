<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\User;

class SemesterTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $ay1Id;
    private $ay2Id;

    protected function setUp(): void
    {
        parent::setUp();
        // Create admin user
        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Create two academic years for testing scoped uniqueness
        $this->ay1Id = DB::table('academic_years')->insertGetId([
            'name'       => 'TA 2025/2026',
            'start_date' => '2025-07-01',
            'end_date'   => '2026-06-30',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->ay2Id = DB::table('academic_years')->insertGetId([
            'name'       => 'TA 2026/2027',
            'start_date' => '2026-07-01',
            'end_date'   => '2027-06-30',
            'is_active'  => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /** @test */
    public function it_can_create_semester_with_unique_data()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.semesters.store'), [
            'academic_year_id' => $this->ay1Id,
            'name'             => 'Ganjil',
            'start_month'      => 7,
            'end_month'        => 12,
        ]);

        $response->assertRedirect(route('admin.semesters.index'));
        $this->assertDatabaseHas('semesters', [
            'academic_year_id' => $this->ay1Id,
            'name'             => 'Ganjil',
            'start_month'      => 7,
            'end_month'        => 12,
        ]);
    }

    /** @test */
    public function it_fails_if_semester_name_is_duplicated_in_same_academic_year()
    {
        // First semester
        DB::table('semesters')->insert([
            'academic_year_id' => $this->ay1Id,
            'name'             => 'Ganjil',
            'start_month'      => 7,
            'end_month'        => 12,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        // Attempt duplicate name, different months
        $response = $this->actingAs($this->admin)->post(route('admin.semesters.store'), [
            'academic_year_id' => $this->ay1Id,
            'name'             => 'Ganjil', // Duplicate
            'start_month'      => 8,
            'end_month'        => 11,
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function it_fails_if_start_month_or_end_month_is_duplicated_in_same_academic_year()
    {
        // First semester
        DB::table('semesters')->insert([
            'academic_year_id' => $this->ay1Id,
            'name'             => 'Ganjil',
            'start_month'      => 7,
            'end_month'        => 12,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        // Attempt duplicate start month
        $response1 = $this->actingAs($this->admin)->post(route('admin.semesters.store'), [
            'academic_year_id' => $this->ay1Id,
            'name'             => 'Ganjil 2',
            'start_month'      => 7, // Duplicate
            'end_month'        => 10,
        ]);
        $response1->assertSessionHasErrors(['start_month']);

        // Attempt duplicate end month
        $response2 = $this->actingAs($this->admin)->post(route('admin.semesters.store'), [
            'academic_year_id' => $this->ay1Id,
            'name'             => 'Ganjil 3',
            'start_month'      => 8,
            'end_month'        => 12, // Duplicate
        ]);
        $response2->assertSessionHasErrors(['end_month']);
    }

    /** @test */
    public function it_can_have_semesters_with_same_name_in_different_academic_years()
    {
        // First semester in AY 1
        DB::table('semesters')->insert([
            'academic_year_id' => $this->ay1Id,
            'name'             => 'Ganjil',
            'start_month'      => 7,
            'end_month'        => 12,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        // Same name and months in AY 2 should be allowed
        $response = $this->actingAs($this->admin)->post(route('admin.semesters.store'), [
            'academic_year_id' => $this->ay2Id,
            'name'             => 'Ganjil',
            'start_month'      => 7,
            'end_month'        => 12,
        ]);

        $response->assertRedirect(route('admin.semesters.index'));
        $this->assertDatabaseHas('semesters', [
            'academic_year_id' => $this->ay2Id,
            'name'             => 'Ganjil',
            'start_month'      => 7,
            'end_month'        => 12,
        ]);
    }

    /** @test */
    public function it_ignores_self_when_updating_semester()
    {
        $id = DB::table('semesters')->insertGetId([
            'academic_year_id' => $this->ay1Id,
            'name'             => 'Ganjil',
            'start_month'      => 7,
            'end_month'        => 12,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        // Update the same record without changing any properties
        $response = $this->actingAs($this->admin)->put(route('admin.semesters.update', $id), [
            'academic_year_id' => $this->ay1Id,
            'name'             => 'Ganjil',
            'start_month'      => 7,
            'end_month'        => 12,
        ]);

        $response->assertRedirect(route('admin.semesters.index'));
    }

    /** @test */
    public function it_fails_if_updated_data_clashes_with_another_semester_in_same_academic_year()
    {
        // First semester
        DB::table('semesters')->insert([
            'academic_year_id' => $this->ay1Id,
            'name'             => 'Ganjil',
            'start_month'      => 7,
            'end_month'        => 12,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        // Second semester
        $id = DB::table('semesters')->insertGetId([
            'academic_year_id' => $this->ay1Id,
            'name'             => 'Genap',
            'start_month'      => 1,
            'end_month'        => 6,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        // Attempt to update second semester's name to clash with first
        $response = $this->actingAs($this->admin)->put(route('admin.semesters.update', $id), [
            'academic_year_id' => $this->ay1Id,
            'name'             => 'Ganjil', // Duplicate of first
            'start_month'      => 1,
            'end_month'        => 6,
        ]);
        $response->assertSessionHasErrors(['name']);

        // Attempt to update second semester's start_month to clash with first
        $response2 = $this->actingAs($this->admin)->put(route('admin.semesters.update', $id), [
            'academic_year_id' => $this->ay1Id,
            'name'             => 'Genap',
            'start_month'      => 7, // Duplicate of first
            'end_month'        => 6,
        ]);
        $response2->assertSessionHasErrors(['start_month']);
    }
}
