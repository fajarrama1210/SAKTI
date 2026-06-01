<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\User;

class PaymentTypeTest extends TestCase
{
    use RefreshDatabase;

    private $admin;

    protected function setUp(): void
    {
        parent::setUp();
        // Create admin user
        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
    }

    /** @test */
    public function it_can_create_payment_type_with_unique_name()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.payment-types.store'), [
            'name'       => 'SPP',
            'is_monthly' => 1,
        ]);

        $response->assertRedirect(route('admin.payment-types.index'));
        $this->assertDatabaseHas('payment_types', [
            'name'       => 'SPP',
            'is_monthly' => 1,
        ]);
    }

    /** @test */
    public function it_fails_if_payment_type_name_is_duplicated()
    {
        // First payment type
        DB::table('payment_types')->insert([
            'name'       => 'SPP',
            'is_monthly' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Attempt duplicate name
        $response = $this->actingAs($this->admin)->post(route('admin.payment-types.store'), [
            'name'       => 'SPP',
            'is_monthly' => 0,
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function it_ignores_self_when_updating_payment_type()
    {
        $id = DB::table('payment_types')->insertGetId([
            'name'       => 'SPP',
            'is_monthly' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update the same record
        $response = $this->actingAs($this->admin)->put(route('admin.payment-types.update', $id), [
            'name'       => 'SPP',
            'is_monthly' => 1,
        ]);

        $response->assertRedirect(route('admin.payment-types.index'));
    }

    /** @test */
    public function it_fails_if_updated_name_clashes_with_another_payment_type()
    {
        // First record
        DB::table('payment_types')->insert([
            'name'       => 'SPP',
            'is_monthly' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Second record
        $id = DB::table('payment_types')->insertGetId([
            'name'       => 'Uang Gedung',
            'is_monthly' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Attempt to update second to match first's name
        $response = $this->actingAs($this->admin)->put(route('admin.payment-types.update', $id), [
            'name'       => 'SPP',
            'is_monthly' => 0,
        ]);

        $response->assertSessionHasErrors(['name']);
    }
}
