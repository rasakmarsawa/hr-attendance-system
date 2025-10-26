<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Employee;
use App\Models\Department;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeDestroyTest extends TestCase
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
    // DESTROY TESTS
    // ---------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_admin_can_delete_employee()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $department = Department::factory()->create();
        $employee = Employee::factory()->create([
            'department_id' => $department->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->delete(route('employee.destroy', $employee->id), [
            '_token' => 'dummy-token',
        ]);

        $response->assertRedirect(route('user.show', $employee->user_id));
        $response->assertSessionHas('success', 'Employee HR data deleted successfully.');

        $this->assertDatabaseMissing('employees', [
            'id' => $employee->id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_employee_cannot_delete_employee()
    {
        $employeeUser = User::factory()->create(['role_id' => $this->employeeRole->id]);
        $department = Department::factory()->create();
        $employee = Employee::factory()->create(['department_id' => $department->id]);

        $response = $this->actingAs($employeeUser)->delete(route('employee.destroy', $employee->id), [
            '_token' => 'dummy-token',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('employees', ['id' => $employee->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function guest_cannot_delete_employee()
    {
        $employee = Employee::factory()->create();

        $response = $this->delete(route('employee.destroy', $employee->id), [
            '_token' => 'dummy-token',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('employees', ['id' => $employee->id]);
    }
}
