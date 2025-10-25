<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class UserCreateStoreTest extends TestCase
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
    public function authenticated_admin_can_view_create_user_form()
    {
        $roles = $this->createRole();
        $admin = $this->createUser($roles,'admin');

        $response = $this->actingAs($admin)->get(route('user.create'));

        $response->assertStatus(200);
        $response->assertViewIs('user.create');
        $response->assertViewHas('roles');
    }

    #[Test]
    public function authenticated_employee_can_not_view_create_user_form()
    {
        $roles = $this->createRole();
        $employee = $this->createUser($roles,'employee');

        $response = $this->actingAs($employee)->get(route('user.create'));

        $response->assertStatus(403);
    }    

    #[Test]
    public function authenticated_admin_can_store_new_user()
    {
        $roles = $this->createRole();
        $admin = $this->createUser($roles,'admin');        

        $formData = [
            '_token' => 'dummy-token',
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role_id' => $roles['employee']->id,
        ];

        $response = $this->actingAs($admin)->post(route('user.store'), $formData);

        $response->assertRedirect(); 
        $this->assertDatabaseHas('users', [            
            'email' => 'johndoe@example.com',
            'role_id' => $roles['employee']->id,
        ]);
    }

    #[Test]
    public function authenticated_employee_can_not_store_new_user()
    {
        $roles = $this->createRole();
        $employee = $this->createUser($roles,'employee');        

        $formData = [
            '_token' => 'dummy-token',
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role_id' => $roles['employee']->id,
        ];

        $response = $this->actingAs($employee)->post(route('user.store'), $formData);

        $response->assertStatus(403); 
    }    
}