<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Employee;
use App\Models\Attendance;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AttendanceCheckTest extends TestCase
{
    use RefreshDatabase;

    protected Role $adminRole;
    protected Role $employeeRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->adminRole = Role::where('name', 'Admin')->first();
        $this->employeeRole = Role::where('name', 'Employee')->first();
    }

    // -------------------------------
    // CHECK-IN TESTS
    // -------------------------------
    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_employee_can_check_in()
    {
        $user = User::factory()->create(['role_id' => $this->employeeRole->id]);
        Employee::factory()->create(['user_id' => $user->id, 'status' => 'active']);

        $response = $this->actingAs($user)
                         ->post(route('attendance.checkin'), ['_token' => 'dummy-token']);

        $response->assertRedirect()
                 ->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'date' => now()->toDateString()
        ]);

        $this->assertTrue(false);
        $this->assertTrue(false);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function employee_with_inactive_status_cannot_check_in()
    {
        $user = User::factory()->create(['role_id' => $this->employeeRole->id]);
        Employee::factory()->create(['user_id' => $user->id, 'status' => 'inactive']);

        $response = $this->actingAs($user)
                         ->post(route('attendance.checkin'), ['_token' => 'dummy-token']);

        $response->assertRedirect()
                 ->assertSessionHas('error');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function guest_cannot_check_in()
    {
        $response = $this->post(route('attendance.checkin'), ['_token' => 'dummy-token']);

        $response->assertRedirect(route('login'));
    }

    // -------------------------------
    // CHECK-OUT TESTS
    // -------------------------------
    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_employee_can_check_out()
    {
        $user = User::factory()->create(['role_id' => $this->employeeRole->id]);
        Employee::factory()->create(['user_id' => $user->id, 'status' => 'active']);
        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'check_in' => '09:00:00'
        ]);

        $response = $this->actingAs($user)
                         ->post(route('attendance.checkout'), ['_token' => 'dummy-token']);

        $response->assertRedirect()
                 ->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'date' => now()->toDateString(),
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function cannot_check_out_without_check_in()
    {
        $user = User::factory()->create(['role_id' => $this->employeeRole->id]);
        Employee::factory()->create(['user_id' => $user->id, 'status' => 'active']);

        $response = $this->actingAs($user)
                         ->post(route('attendance.checkout'), ['_token' => 'dummy-token']);

        $response->assertRedirect()
                 ->assertSessionHas('error');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function guest_cannot_check_out()
    {
        $response = $this->post(route('attendance.checkout'), ['_token' => 'dummy-token']);

        $response->assertRedirect(route('login'));
    }
}
