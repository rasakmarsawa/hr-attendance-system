<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Employee;
use App\Models\Department;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeEditUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected Role $adminRole;
    protected Role $employeeRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Use the RoleSeeder instead of manually creating roles
        $this->seed(RoleSeeder::class);

        $this->adminRole = Role::where('name', 'Admin')->first();
        $this->employeeRole = Role::where('name', 'Employee')->first();
    }

    // ---------------------------------
    // EDIT TESTS
    // ---------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_admin_can_view_edit_page()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $department = Department::factory()->create();
        $employee = Employee::factory()->create([
            'department_id' => $department->id,
        ]);

        $response = $this->actingAs($admin)->get(route('employee.edit', $employee->id));

        $response->assertStatus(200);
        $response->assertViewIs('employee.edit');
        $response->assertViewHasAll(['employee', 'departments']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_employee_cannot_view_edit_page()
    {
        $employeeUser = User::factory()->create(['role_id' => $this->employeeRole->id]);
        $department = Department::factory()->create();
        $employee = Employee::factory()->create(['department_id' => $department->id]);

        $response = $this->actingAs($employeeUser)->get(route('employee.edit', $employee->id));

        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function guest_cannot_view_edit_page()
    {
        $employee = Employee::factory()->create();

        $response = $this->get(route('employee.edit', $employee->id));

        $response->assertRedirect(route('login'));
    }

    // ---------------------------------
    // UPDATE TESTS
    // ---------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_admin_can_update_employee()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $department = Department::factory()->create();
        $newDepartment = Department::factory()->create();
        $employee = Employee::factory()->create([
            'department_id' => $department->id,
            'status' => 'active',
        ]);

        $data = [
            '_token' => 'dummy-token',
            'department_id' => $newDepartment->id,
            'position' => 'Lead Engineer',
            'join_date' => '2025-03-01',
            'daily_rate' => 400000,
            'status' => 'inactive',
        ];

        $response = $this->actingAs($admin)->put(route('employee.update', $employee->id), $data);

        $response->assertRedirect(route('user.show', $employee->user_id));
        $response->assertSessionHas('success', 'Employee HR data updated successfully.');

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'department_id' => $newDepartment->id,
            'position' => 'Lead Engineer',
            'status' => 'inactive',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_employee_cannot_update_employee()
    {
        $employeeUser = User::factory()->create(['role_id' => $this->employeeRole->id]);
        $department = Department::factory()->create();
        $employee = Employee::factory()->create(['department_id' => $department->id]);

        $data = [
            '_token' => 'dummy-token',
            'department_id' => $department->id,
            'position' => 'HR Staff',
            'join_date' => '2025-04-01',
            'daily_rate' => 150000,
            'status' => 'active',
        ];

        $response = $this->actingAs($employeeUser)->put(route('employee.update', $employee->id), $data);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('employees', ['position' => 'HR Staff']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function guest_cannot_update_employee()
    {
        $employee = Employee::factory()->create();
        $department = Department::factory()->create();

        $data = [
            '_token' => 'dummy-token',
            'department_id' => $department->id,
            'position' => 'Intern',
            'join_date' => '2025-04-01',
            'daily_rate' => 100000,
            'status' => 'active',
        ];

        $response = $this->put(route('employee.update', $employee->id), $data);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('employees', ['position' => 'Intern']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function update_fails_if_validation_invalid()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $department = Department::factory()->create();
        $employee = Employee::factory()->create(['department_id' => $department->id]);

        $data = [
            '_token' => 'dummy-token',
            'department_id' => null,
            'position' => '',
            'join_date' => 'not-a-date',
            'daily_rate' => -100,
            'status' => 'wrong',
        ];

        $response = $this->actingAs($admin)->put(route('employee.update', $employee->id), $data);

        $response->assertSessionHasErrors([
            'department_id', 'position', 'join_date', 'daily_rate', 'status',
        ]);

        // unchanged
        $this->assertDatabaseMissing('employees', ['position' => '']);
    }
}
