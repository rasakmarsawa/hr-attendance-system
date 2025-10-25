<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;

class UserEditUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function createRoles()
    {
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $employeeRole = Role::firstOrCreate(['name' => 'Employee']);

        return [
            'admin' => $adminRole,
            'employee' => $employeeRole,
        ];
    }

    protected function createUser($roles, $role)
    {
        return User::factory()->create([
            'role_id' => $roles[$role]->id,
            'password' => Hash::make('password'), // ensure login works
        ]);
    }

    #[Test]
    public function admin_can_view_edit_user_form()
    {
        $roles = $this->createRoles();
        $admin = $this->createUser($roles, 'admin');
        $employee = $this->createUser($roles, 'employee');

        $response = $this->actingAs($admin)->get(route('user.edit', $employee));

        $response->assertStatus(200);
        $response->assertViewIs('user.edit');
        $response->assertViewHas('user', $employee);
        $response->assertViewHas('roles', function ($viewRoles) use ($roles) {
            return $viewRoles->count() === count($roles);
        });
    }

    #[Test]
    public function admin_can_update_user()
    {
        $roles = $this->createRoles();
        $admin = $this->createUser($roles, 'admin');
        $employee = $this->createUser($roles, 'employee');

        $formData = [
            '_token' => 'dummy-token',
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'role_id' => $roles['employee']->id,
        ];

        $response = $this->actingAs($admin)
                         ->put(route('user.update', $employee), $formData);

        $response->assertRedirect(route('user.show', $employee));
        $this->assertDatabaseHas('users', [
            'id' => $employee->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'role_id' => $roles['employee']->id,
        ]);
    }

    #[Test]
    public function guest_cannot_access_edit_or_update()
    {
        $roles = $this->createRoles();
        $employee = $this->createUser($roles, 'employee');

        // edit page
        $response = $this->get(route('user.edit', $employee));
        $response->assertRedirect('/login');

        // update action
        $response = $this->put(route('user.update', $employee), [
            '_token' => 'dummy-token',
            'name' => 'Hacker',
            'email' => 'hacker@example.com',
            'role_id' => $roles['employee']->id,
        ]);
        $response->assertRedirect('/login');
    }
}
