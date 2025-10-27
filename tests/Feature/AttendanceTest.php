<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Employee;
use App\Models\Attendance;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected Role $adminRole;
    protected Role $employeeRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles
        $this->seed(RoleSeeder::class);

        $this->adminRole = Role::where('name', 'Admin')->first();
        $this->employeeRole = Role::where('name', 'Employee')->first();
    }

    // -------------------------------
    // INDEX TEST
    // -------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_view_attendance_index()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        Attendance::factory()->count(3)->create();

        $response = $this->actingAs($admin)
                         ->get(route('attendance.index'));

        $response->assertStatus(200)
                 ->assertViewIs('attendance.index')
                 ->assertViewHas('attendances');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function employee_cannot_view_attendance_index()
    {
        $employee = User::factory()->create(['role_id' => $this->employeeRole->id]);

        $response = $this->actingAs($employee)
                         ->get(route('attendance.index'));

        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function guest_is_redirected_from_attendance_index()
    {
        $response = $this->get(route('attendance.index'));

        $response->assertRedirect(route('login'));
    }

    // -------------------------------
    // STORE (PRE-FILL) TEST
    // -------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_pre_fill_attendance()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $activeEmployee = User::factory()->create();
        Employee::factory()->create([
            'user_id' => $activeEmployee->id,
            'status' => 'active'
        ]);

        $response = $this->actingAs($admin)
                         ->post(route('attendance.store'),['_token'=>'dummy-token']);

        $response->assertRedirect(route('attendance.index'))
                 ->assertSessionHas('success', 'Attendance records pre-filled for today.');

        $this->assertDatabaseHas('attendances', [
            'user_id' => $activeEmployee->id,
            'status' => 'absent',
            'date' => now()->format('Y-m-d')
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function employee_cannot_pre_fill_attendance()
    {
        $employee = User::factory()->create(['role_id' => $this->employeeRole->id]);

        $response = $this->actingAs($employee)
                        ->post(route('attendance.store'), [
                            '_token' => 'dummy-token'
                        ]);

        $response->assertStatus(403);
        $this->assertDatabaseCount('attendances', 0);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function guest_cannot_pre_fill_attendance()
    {
        $response = $this->post(route('attendance.store'), [
            '_token' => 'dummy-token'
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('attendances', 0);
    }

    // -------------------------------
    // REPORT TEST
    // -------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_view_attendance_report()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        $response = $this->actingAs($admin)
                         ->get(route('attendance.report', ['month' => now()->month, 'year' => now()->year]));

        $response->assertStatus(200)
                 ->assertViewIs('attendance.report')
                 ->assertViewHasAll(['report', 'month', 'year']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function employee_cannot_view_attendance_report()
    {
        $employee = User::factory()->create(['role_id' => $this->employeeRole->id]);

        $response = $this->actingAs($employee)
                         ->get(route('attendance.report', ['month' => now()->month, 'year' => now()->year]));

        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function guest_cannot_view_attendance_report()
    {
        $response = $this->get(route('attendance.report', ['month' => now()->month, 'year' => now()->year]));

        $response->assertRedirect(route('login'));
    }

    // -------------------------------
    // DETAIL TEST
    // -------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_view_attendance_detail()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $user = User::factory()->create();
        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => now()->format('Y-m-d'),
            'status' => 'present'
        ]);

        $response = $this->actingAs($admin)
                         ->get(route('attendance.detail', [$user->id, now()->month, now()->year]));

        $response->assertStatus(200)
                 ->assertViewIs('attendance.detail')
                 ->assertViewHasAll(['attendances', 'user', 'month', 'year']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function employee_cannot_view_attendance_detail()
    {
        $employee = User::factory()->create(['role_id' => $this->employeeRole->id]);
        $user = User::factory()->create();

        $response = $this->actingAs($employee)
                         ->get(route('attendance.detail', [$user->id, now()->month, now()->year]));

        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function guest_cannot_view_attendance_detail()
    {
        $user = User::factory()->create();

        $response = $this->get(route('attendance.detail', [$user->id, now()->month, now()->year]));

        $response->assertRedirect(route('login'));
    }

    // -------------------------------
    // EXPORT TEST
    // -------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_access_attendance_export()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        $response = $this->actingAs($admin)
                        ->get(route('attendance.export'));

        $response->assertStatus(200);
        // Optional: check response type if exporting file (csv, excel)
        // $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function employee_cannot_access_attendance_export()
    {
        $employee = User::factory()->create(['role_id' => $this->employeeRole->id]);

        $response = $this->actingAs($employee)
                        ->get(route('attendance.export'));

        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function guest_cannot_access_attendance_export()
    {
        $response = $this->get(route('attendance.export'));

        $response->assertRedirect(route('login'));
    }

}
