<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use \PHPUnit\Framework\Attributes\Test;

class DepartmentIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function createRole()
    {
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $employeeRole = Role::firstOrCreate(['name' => 'Employee']);

        return [
            'admin' => $adminRole,
            'employee' => $employeeRole,
        ];           
    }

    protected function createUser($roles,$role)
    {
        $user = User::factory()->create([
            'role_id' => $roles[$role]->id,
        ]);
        return $user;
    }    

    #[Test]
    public function authenticated_admin_can_view_department_index()
    {
        $roles = $this->createRole();
        $admin = $this->createUser($roles,'admin');
        $departments = Department::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get(route('department.index'));

        $response->assertStatus(200);
        $response->assertViewIs('department.index');
        $response->assertViewHas('departments', function ($viewDepartments) use ($departments) {
            return $viewDepartments->contains($departments->first());
        });
    }

    #[Test]
    public function authenticated_employee_can_not_view_department_index()
    {
        $roles = $this->createRole();
        $employee = $this->createUser($roles,'employee');
        $departments = Department::factory()->count(3)->create();

        $response = $this->actingAs($employee)->get(route('department.index'));

        $response->assertStatus(403);        
    }    

    #[Test]
    public function guest_cannot_access_department_index()
    {
        $response = $this->get(route('department.index'));

        $response->assertRedirect(route('login'));
    }
}
