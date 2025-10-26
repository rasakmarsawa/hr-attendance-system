<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DepartmentDestroyTest extends TestCase
{
    use RefreshDatabase;

    protected Role $adminRole;
    protected Role $employeeRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup fixed roles
        $this->adminRole = Role::create(['name' => 'Admin']);
        $this->employeeRole = Role::create(['name' => 'Employee']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_admin_can_delete_department()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $department = Department::factory()->create();

        $response = $this->actingAs($admin)->delete(
            route('department.destroy', $department),
            ['_token' => 'dummy-token']
        );

        $response->assertRedirect(route('department.index'));
        $response->assertSessionHas('success', 'Department deleted successfully.');

        $this->assertDatabaseMissing('departments', ['id' => $department->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_employee_cannot_delete_department()
    {
        $employee = User::factory()->create(['role_id' => $this->employeeRole->id]);
        $department = Department::factory()->create();

        $response = $this->actingAs($employee)->delete(
            route('department.destroy', $department),
            ['_token' => 'dummy-token']
        );

        $response->assertStatus(403);
        $this->assertDatabaseHas('departments', ['id' => $department->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function guest_cannot_delete_department()
    {
        $department = Department::factory()->create();

        $response = $this->delete(
            route('department.destroy', $department),
            ['_token' => 'dummy-token']
        );

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('departments', ['id' => $department->id]);
    }
}
