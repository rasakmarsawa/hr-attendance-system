<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\Employee;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeCreateStoreTest extends TestCase
{
    use RefreshDatabase;

    protected Role $adminRole;
    protected Role $employeeRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles instead of manual creation
        $this->seed(RoleSeeder::class);

        $this->adminRole = Role::where('name', 'Admin')->first();
        $this->employeeRole = Role::where('name', 'Employee')->first();
    }

    // ---------------------------------
    // CREATE TESTS
    // ---------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_admin_can_view_employee_create_page()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $targetUser = User::factory()->create();
        Department::factory()->count(2)->create();

        $response = $this->actingAs($admin)
            ->get(route('employee.create', $targetUser->id));

        $response->assertStatus(200)
                 ->assertViewIs('employee.create')
                 ->assertViewHasAll(['departments', 'user']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_employee_cannot_view_employee_create_page()
    {
        $employee = User::factory()->create(['role_id' => $this->employeeRole->id]);
        $targetUser = User::factory()->create();

        $response = $this->actingAs($employee)
            ->get(route('employee.create', $targetUser->id));

        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function guest_cannot_view_employee_create_page()
    {
        $targetUser = User::factory()->create();

        $response = $this->get(route('employee.create', $targetUser->id));

        $response->assertRedirect(route('login'));
    }

    // ---------------------------------
    // STORE TESTS
    // ---------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_admin_can_store_employee_data()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $targetUser = User::factory()->create();
        $department = Department::factory()->create();

        $employeeData = Employee::factory()->make([
            'user_id' => $targetUser->id,
            'department_id' => $department->id,
        ])->toArray();

        $employeeData['_token'] = 'dummy-token';

        $response = $this->actingAs($admin)
            ->post(route('employee.store'), $employeeData);

        $response->assertRedirect(route('user.show', $targetUser->id))
                 ->assertSessionHas('success', 'Employee HR data created successfully.');

        $this->assertDatabaseHas('employees', [
            'user_id' => $targetUser->id,
            'department_id' => $department->id,
            'position' => $employeeData['position'],
            'status' => $employeeData['status'],
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_employee_cannot_store_employee_data()
    {
        $employee = User::factory()->create(['role_id' => $this->employeeRole->id]);
        $targetUser = User::factory()->create();
        $department = Department::factory()->create();

        $employeeData = Employee::factory()->make([
            'user_id' => $targetUser->id,
            'department_id' => $department->id,
        ])->toArray();

        $employeeData['_token'] = 'dummy-token';

        $response = $this->actingAs($employee)
            ->post(route('employee.store'), $employeeData);

        $response->assertStatus(403);
        $this->assertDatabaseCount('employees', 0);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function guest_cannot_store_employee_data()
    {
        $targetUser = User::factory()->create();
        $department = Department::factory()->create();

        $employeeData = Employee::factory()->make([
            'user_id' => $targetUser->id,
            'department_id' => $department->id,
        ])->toArray();

        $employeeData['_token'] = 'dummy-token';

        $response = $this->post(route('employee.store'), $employeeData);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('employees', 0);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function store_fails_if_validation_invalid()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        $response = $this->actingAs($admin)
            ->post(route('employee.store'), [
                '_token' => 'dummy-token',
                'user_id' => null,
                'department_id' => null,
                'position' => '',
                'join_date' => 'invalid-date',
                'daily_rate' => -5,
                'status' => 'unknown',
            ]);

        $response->assertSessionHasErrors([
            'user_id',
            'department_id',
            'position',
            'join_date',
            'daily_rate',
            'status',
        ]);

        $this->assertDatabaseCount('employees', 0);
    }
}
