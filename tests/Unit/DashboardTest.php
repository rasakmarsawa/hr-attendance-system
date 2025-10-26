<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function authenticated_user_can_access_dashboard(): void    
    {
        $role = Role::create([
            'name' => 'Admin',
        ]);

        $user = User::factory()->create();
        $user->role()->associate($role);
        $user->save();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.dashboard');

        $this->assertEquals('Admin', auth()->user()->role->name);
    }

    #[Test]
    public function guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }
}
