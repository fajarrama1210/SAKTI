<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\User;

class AcademicYearTest extends TestCase
{
    use RefreshDatabase;

    private $admin;

    protected function setUp(): void
    {
        parent::setUp();
        // Create an admin user
        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
    }

    /** @test */
    public function it_can_create_academic_year_with_valid_unique_dates()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.academic-years.store'), [
            'name' => 'TA 2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.academic-years.index'));
        $this->assertDatabaseHas('academic_years', [
            'name' => 'TA 2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
        ]);
    }

    /** @test */
    public function it_fails_if_dates_are_not_unique_on_store()
    {
        // First academic year
        DB::table('academic_years')->insert([
            'name' => 'TA 2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Attempt duplicate start date
        $response1 = $this->actingAs($this->admin)->post(route('admin.academic-years.store'), [
            'name' => 'TA 2025/2026 Alt',
            'start_date' => '2025-07-01', // Duplicate
            'end_date' => '2026-06-29',
        ]);
        $response1->assertSessionHasErrors(['start_date']);

        // Attempt duplicate end date
        $response2 = $this->actingAs($this->admin)->post(route('admin.academic-years.store'), [
            'name' => 'TA 2025/2026 Alt 2',
            'start_date' => '2025-07-02',
            'end_date' => '2026-06-30', // Duplicate
        ]);
        $response2->assertSessionHasErrors(['end_date']);
    }

    /** @test */
    public function it_ignores_current_academic_year_when_updating()
    {
        $id = DB::table('academic_years')->insertGetId([
            'name' => 'TA 2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update the same academic year without changing name or dates
        $response = $this->actingAs($this->admin)->put(route('admin.academic-years.update', $id), [
            'name' => 'TA 2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.academic-years.index'));
    }

    /** @test */
    public function it_fails_if_dates_are_not_unique_on_update()
    {
        // First academic year
        DB::table('academic_years')->insert([
            'name' => 'TA 2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Second academic year
        $id = DB::table('academic_years')->insertGetId([
            'name' => 'TA 2026/2027',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'is_active' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Attempt to update second academic year's start date to match the first's
        $response = $this->actingAs($this->admin)->put(route('admin.academic-years.update', $id), [
            'name' => 'TA 2026/2027',
            'start_date' => '2025-07-01', // Duplicate of first
            'end_date' => '2027-06-30',
        ]);

        $response->assertSessionHasErrors(['start_date']);
    }
}
