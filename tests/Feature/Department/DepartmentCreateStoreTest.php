<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DepartmentCreateStoreTest extends TestCase
{
    use RefreshDatabase;

    protected Role $adminRole;
    protected Role $employeeRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles for testing
        $this->adminRole = Role::create(['name' => 'Admin']);
        $this->employeeRole = Role::create(['name' => 'Employee']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_admin_can_view_department_create_page()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        $response = $this->actingAs($admin)->get(route('department.create'));

        $response->assertStatus(200);
        $response->assertViewIs('department.create');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_employee_cannot_view_department_create_page()
    {
        $employee = User::factory()->create(['role_id' => $this->employeeRole->id]);

        $response = $this->actingAs($employee)->get(route('department.create'));

        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function guest_cannot_access_create_page()
    {
        $response = $this->get(route('department.create'));
        $response->assertRedirect(route('login'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_admin_can_store_valid_department()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        $data = [
            '_token' => 'dummy-token',
            'name' => 'Human Resources',
            'code_prefix' => 'HR',
            'description' => 'Handles all employee matters.',
        ];

        $response = $this->actingAs($admin)->post(route('department.store'), $data);

        $response->assertRedirect(route('department.index'));
        $this->assertDatabaseHas('departments', [
            'name' => 'Human Resources',
            'code_prefix' => 'HR',
        ]);

        $response->assertSessionHas('success', 'Department created successfully.');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_employee_cannot_store_department()
    {
        $employee = User::factory()->create(['role_id' => $this->employeeRole->id]);

        $data = [
            '_token' => 'dummy-token',
            'name' => 'Finance',
            'code_prefix' => 'FIN',
            'description' => 'Should not be created by employee',
        ];

        $response = $this->actingAs($employee)->post(route('department.store'), $data);

        $response->assertStatus(403);
        $this->assertDatabaseCount('departments', 0);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fails_validation_if_name_is_missing()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        $response = $this->actingAs($admin)->post(route('department.store'), [
            '_token' => 'dummy-token',
            'code_prefix' => 'HR',
            'description' => 'Missing name field.',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('departments', 0);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fails_validation_if_name_or_code_prefix_is_not_unique()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        Department::factory()->create([
            'name' => 'Finance',
            'code_prefix' => 'FIN',
        ]);

        $response = $this->actingAs($admin)->post(route('department.store'), [
            '_token' => 'dummy-token',
            'name' => 'Finance', // duplicate
            'code_prefix' => 'FIN', // duplicate
            'description' => 'Duplicate test',
        ]);

        $response->assertSessionHasErrors(['name', 'code_prefix']);
        $this->assertDatabaseCount('departments', 1);
    }
}
