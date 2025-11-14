<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class UserDestroyTest extends TestCase
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
    public function authenticated_admin_can_destroy_user()
    {
        $roles = $this->createRole();
        $admin = $this->createUser($roles,'admin');
        $deleteEmployee = $this->createUser($roles,'employee');

        $response = $this->actingAs($admin)->delete(route('user.destroy',$deleteEmployee),[
            '_token' => 'dummy-token',
        ]);

        $response->assertRedirect(route('user.index'));

        $this->assertDatabaseMissing('users', [
            'id' => $deleteEmployee->id,
        ]);
    }
 
    #[Test]
    public function authenticated_employee_can_not_destroy_user()
    {
        $roles = $this->createRole();        
        $employee = $this->createUser($roles,'employee');
        $deleteEmployee = $this->createUser($roles,'employee');

        $response = $this->actingAs($employee)->delete(route('user.destroy',$deleteEmployee),[
            '_token' => 'dummy-token',
        ]);

        $response->assertStatus(403);
    }    
}
