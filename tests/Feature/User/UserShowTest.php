<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class UserShowTest extends TestCase
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
    public function authenticated_admin_can_view_user_show_page()
    {
        $roles = $this->createRole();
        $admin = $this->createUser($roles,'admin');

        $response = $this->actingAs($admin)->get(route('user.show',$admin));

        $response->assertStatus(200);
        $response->assertViewIs('user.show');
    }
 
    #[Test]
    public function authenticated_employee_can_not_view_user_show_page()
    {
        $roles = $this->createRole();        
        $employee = $this->createUser($roles,'employee');

        $response = $this->actingAs($employee)->get(route('user.show',$employee));

        $response->assertStatus(403);
    }    
}
