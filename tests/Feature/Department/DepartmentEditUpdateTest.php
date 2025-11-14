<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DepartmentEditUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected Role $adminRole;
    protected Role $employeeRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles for test context
        $this->adminRole = Role::create(['name' => 'Admin']);
        $this->employeeRole = Role::create(['name' => 'Employee']);
    }

    // -------------------------------
    // EDIT TESTS
    // -------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_admin_can_view_edit_page()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $department = Department::factory()->create();

        $response = $this->actingAs($admin)->get(route('department.edit', $department));

        $response->assertStatus(200);
        $response->assertViewIs('department.edit');
        $response->assertViewHas('department', $department);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_employee_cannot_view_edit_page()
    {
        $employee = User::factory()->create(['role_id' => $this->employeeRole->id]);
        $department = Department::factory()->create();

        $response = $this->actingAs($employee)->get(route('department.edit', $department));

        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function guest_cannot_view_edit_page()
    {
        $department = Department::factory()->create();

        $response = $this->get(route('department.edit', $department));

        $response->assertRedirect(route('login'));
    }

    // -------------------------------
    // UPDATE TESTS
    // -------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_admin_can_update_department()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $department = Department::factory()->create([
            'name' => 'Finance',
            'code_prefix' => 'FIN',
        ]);

        $updatedData = [
            '_token' => 'dummy-token',
            'name' => 'Finance & Accounting',
            'code_prefix' => 'FA',
            'description' => 'Updated department details.',
        ];

        $response = $this->actingAs($admin)->put(route('department.update', $department), $updatedData);

        $response->assertRedirect(route('department.index'));
        $response->assertSessionHas('success', 'Department updated successfully.');

        $this->assertDatabaseHas('departments', [
            'id' => $department->id,
            'name' => 'Finance & Accounting',
            'code_prefix' => 'FA',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_employee_cannot_update_department()
    {
        $employee = User::factory()->create(['role_id' => $this->employeeRole->id]);
        $department = Department::factory()->create();

        $data = [
            '_token' => 'dummy-token',
            'name' => 'Updated Name',
            'code_prefix' => 'NEW',
            'description' => 'Attempted by employee',
        ];

        $response = $this->actingAs($employee)->put(route('department.update', $department), $data);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('departments', ['name' => 'Updated Name']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function guest_cannot_update_department()
    {
        $department = Department::factory()->create();

        $response = $this->put(route('department.update', $department), [
            '_token' => 'dummy-token',
            'name' => 'Unauthorized Update',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('departments', ['name' => 'Unauthorized Update']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function update_fails_validation_if_name_is_missing()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $department = Department::factory()->create([
            'name' => 'Marketing',
            'code_prefix' => 'MKT',
        ]);

        $response = $this->actingAs($admin)->put(route('department.update', $department), [
            '_token' => 'dummy-token',
            'name' => '',
            'code_prefix' => 'MKT2',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseHas('departments', ['name' => 'Marketing']); // unchanged
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function update_fails_if_name_or_code_prefix_is_duplicate()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        $dep1 = Department::factory()->create(['name' => 'Finance', 'code_prefix' => 'FIN']);
        $dep2 = Department::factory()->create(['name' => 'HR', 'code_prefix' => 'HR']);

        $response = $this->actingAs($admin)->put(route('department.update', $dep2), [
            '_token' => 'dummy-token',
            'name' => 'Finance', // duplicate
            'code_prefix' => 'FIN', // duplicate
        ]);

        $response->assertSessionHasErrors(['name', 'code_prefix']);
    }
}
