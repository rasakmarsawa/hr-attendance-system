<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Employee;
use App\Models\Payroll;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PayrollMyPayrollTest extends TestCase
{
    use RefreshDatabase;

    protected Role $adminRole;
    protected Role $employeeRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->adminRole = Role::where('name', 'Admin')->first();
        $this->employeeRole = Role::where('name', 'Employee')->first();
    }

    // -------------------------------
    // MY PAYROLL - GET
    // -------------------------------
    #[\PHPUnit\Framework\Attributes\Test]
    public function employee_can_view_my_payroll()
    {
        $user = User::factory()->create(['role_id' => $this->employeeRole->id]);
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        Payroll::factory()->count(3)->create([
            'employee_id' => $employee->id,
            'status' => 'finalized'
        ]);

        $response = $this->actingAs($user)
                         ->get(route('payroll.myPayroll'));

        $response->assertStatus(200)
                 ->assertViewIs('payroll.my_payroll')
                 ->assertViewHas('payrolls');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_without_employee_record_cannot_view_my_payroll()
    {
        $user = User::factory()->create(['role_id' => $this->employeeRole->id]);

        $response = $this->actingAs($user)
                         ->get(route('payroll.myPayroll'));

        $response->assertRedirect(route('dashboard'))
                 ->assertSessionHas('error');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function guest_cannot_view_my_payroll()
    {
        $response = $this->get(route('payroll.myPayroll'));

        $response->assertRedirect(route('login'));
    }

    // -------------------------------
    // EXPORT ONE PAYROLL - GET
    // -------------------------------
    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_export_single_payroll()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $payroll = Payroll::factory()->create();

        $response = $this->actingAs($admin)
                         ->get(route('payroll.exportOne', $payroll->id));

        $response->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function employee_can_export_single_payroll()
    {
        $employee = User::factory()->create(['role_id' => $this->employeeRole->id]);
        $payroll = Payroll::factory()->create();

        $response = $this->actingAs($employee)
                        ->get(route('payroll.exportOne', $payroll->id));

        $response->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function guest_cannot_export_single_payroll()
    {
        $payroll = Payroll::factory()->create();

        $response = $this->get(route('payroll.exportOne', $payroll->id));

        $response->assertRedirect(route('login'));
    }
}
