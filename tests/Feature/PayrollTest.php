<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Department;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PayrollTest extends TestCase
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
    // INDEX
    // -------------------------------
    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_view_payroll_index()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $month = now()->month;
        $year = now()->year;

        Payroll::factory()->count(3)->create(['month' => $month, 'year' => $year]);

        $response = $this->actingAs($admin)
                         ->get(route('payroll.index', [$month, $year]));

        $response->assertStatus(200)
                 ->assertViewIs('payroll.index')
                 ->assertViewHasAll(['payrolls', 'month', 'year']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function employee_cannot_view_payroll_index()
    {
        $employee = User::factory()->create(['role_id' => $this->employeeRole->id]);
        $month = now()->month;
        $year = now()->year;

        $response = $this->actingAs($employee)
                         ->get(route('payroll.index', [$month, $year]));

        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function guest_cannot_view_payroll_index()
    {
        $month = now()->month;
        $year = now()->year;

        $response = $this->get(route('payroll.index', [$month, $year]));

        $response->assertRedirect(route('login'));
    }

    // -------------------------------
    // EDIT
    // -------------------------------
    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_view_payroll_edit()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $payroll = Payroll::factory()->create();

        $response = $this->actingAs($admin)
                         ->get(route('payroll.edit', $payroll->id));

        $response->assertStatus(200)
                 ->assertViewIs('payroll.edit')
                 ->assertViewHasAll(['payroll', 'departments']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function employee_cannot_view_payroll_edit()
    {
        $employee = User::factory()->create(['role_id' => $this->employeeRole->id]);
        $payroll = Payroll::factory()->create();

        $response = $this->actingAs($employee)
                         ->get(route('payroll.edit', $payroll->id));

        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function guest_cannot_view_payroll_edit()
    {
        $payroll = Payroll::factory()->create();

        $response = $this->get(route('payroll.edit', $payroll->id));

        $response->assertRedirect(route('login'));
    }

    // -------------------------------
    // UPDATE
    // -------------------------------
    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_update_payroll()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $payroll = Payroll::factory()->create(['status' => 'draft']);

        $updateData = [
            '_token' => 'dummy-token',
            'total_present' => 10,
            'total_absent'  => 2,
            'total_late'    => 1,
            'daily_rate'    => 100,
            'total_pay'     => 1100,
            'department_name' => 'HR'
        ];

        $response = $this->actingAs($admin)
                         ->put(route('payroll.update', $payroll->id), $updateData);

        $response->assertRedirect(route('payroll.index', ['month' => $payroll->month, 'year' => $payroll->year]))
                 ->assertSessionHas('success');

        $this->assertDatabaseHas('payrolls', [
            'id' => $payroll->id,
            'total_present' => 10,
            'total_absent' => 2,
            'total_late' => 1,
            'daily_rate' => 100,
            'total_pay' => 1100,
            'department_name' => 'HR'
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function employee_cannot_update_payroll()
    {
        $employee = User::factory()->create(['role_id' => $this->employeeRole->id]);
        $payroll = Payroll::factory()->create();

        $updateData = [
            '_token' => 'dummy-token',
            'total_present' => 10,
            'total_absent'  => 2,
            'total_late'    => 1,
            'daily_rate'    => 100,
            'total_pay'     => 1100,
            'department_name' => 'HR'
        ];

        $response = $this->actingAs($employee)
                         ->put(route('payroll.update', $payroll->id), $updateData);

        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function guest_cannot_update_payroll()
    {
        $payroll = Payroll::factory()->create();

        $updateData = [
            '_token' => 'dummy-token',
            'total_present' => 10,
            'total_absent'  => 2,
            'total_late'    => 1,
            'daily_rate'    => 100,
            'total_pay'     => 1100,
            'department_name' => 'HR'
        ];

        $response = $this->put(route('payroll.update', $payroll->id), $updateData);

        $response->assertRedirect(route('login'));
    }

    // -------------------------------
    // GENERATE PAYROLL (bulk) - POST
    // -------------------------------
    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_generate_payroll()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $month = now()->month;
        $year = now()->year;

        $response = $this->actingAs($admin)
                        ->post(route('payroll.generate', [$month, $year]), ['_token' => 'dummy-token']);

        $response->assertRedirect()
                ->assertSessionHas('success');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function employee_cannot_generate_payroll()
    {
        $employee = User::factory()->create(['role_id' => $this->employeeRole->id]);
        $month = now()->month;
        $year = now()->year;

        $response = $this->actingAs($employee)
                        ->post(route('payroll.generate', [$month, $year]), ['_token' => 'dummy-token']);

        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function guest_cannot_generate_payroll()
    {
        $month = now()->month;
        $year = now()->year;

        $response = $this->post(route('payroll.generate', [$month, $year]), ['_token' => 'dummy-token']);

        $response->assertRedirect(route('login'));
    }

    // -------------------------------
    // FINALIZE ALL (bulk) - POST
    // -------------------------------
    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_finalize_all_payrolls()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $month = now()->month;
        $year = now()->year;

        Payroll::factory()->count(3)->create(['month' => $month, 'year' => $year, 'status' => 'draft']);

        $response = $this->actingAs($admin)
                        ->post(route('payroll.finalizeAll', [$month, $year]), ['_token' => 'dummy-token']);

        $response->assertRedirect()
                ->assertSessionHas('success');

        $this->assertDatabaseCount('payrolls', 3);
        $this->assertDatabaseHas('payrolls', ['status' => 'finalized']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function employee_cannot_finalize_all_payrolls()
    {
        $employee = User::factory()->create(['role_id' => $this->employeeRole->id]);
        $month = now()->month;
        $year = now()->year;

        $response = $this->actingAs($employee)
                        ->post(route('payroll.finalizeAll', [$month, $year]), ['_token' => 'dummy-token']);

        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function guest_cannot_finalize_all_payrolls()
    {
        $month = now()->month;
        $year = now()->year;

        $response = $this->post(route('payroll.finalizeAll', [$month, $year]), ['_token' => 'dummy-token']);

        $response->assertRedirect(route('login'));
    }

    // -------------------------------
    // FINALIZE SINGLE - POST
    // -------------------------------
    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_finalize_single_payroll()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $payroll = Payroll::factory()->create(['status' => 'draft']);

        $response = $this->actingAs($admin)
                        ->post(route('payroll.finalize', $payroll->id), ['_token' => 'dummy-token']);

        $response->assertRedirect()
                ->assertSessionHas('success');

        $this->assertDatabaseHas('payrolls', [
            'id' => $payroll->id,
            'status' => 'finalized'
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function employee_cannot_finalize_single_payroll()
    {
        $employee = User::factory()->create(['role_id' => $this->employeeRole->id]);
        $payroll = Payroll::factory()->create(['status' => 'draft']);

        $response = $this->actingAs($employee)
                        ->post(route('payroll.finalize', $payroll->id), ['_token' => 'dummy-token']);

        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function guest_cannot_finalize_single_payroll()
    {
        $payroll = Payroll::factory()->create(['status' => 'draft']);

        $response = $this->post(route('payroll.finalize', $payroll->id), ['_token' => 'dummy-token']);

        $response->assertRedirect(route('login'));
    }

    // -------------------------------
    // PAY SINGLE - PUT
    // -------------------------------
    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_mark_payroll_as_paid()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $payroll = Payroll::factory()->create();

        $response = $this->actingAs($admin)
                        ->put(route('payroll.pay', $payroll->id), ['_token' => 'dummy-token']);

        $response->assertRedirect()
                ->assertSessionHas('success');

        $this->assertDatabaseHas('payrolls', [
            'id' => $payroll->id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function employee_cannot_mark_payroll_as_paid()
    {
        $employee = User::factory()->create(['role_id' => $this->employeeRole->id]);
        $payroll = Payroll::factory()->create();

        $response = $this->actingAs($employee)
                        ->put(route('payroll.pay', $payroll->id), ['_token' => 'dummy-token']);

        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function guest_cannot_mark_payroll_as_paid()
    {
        $payroll = Payroll::factory()->create();

        $response = $this->put(route('payroll.pay', $payroll->id), ['_token' => 'dummy-token']);

        $response->assertRedirect(route('login'));
    }

    // -------------------------------
    // EXPORT - GET
    // -------------------------------
    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_export_payroll()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $month = now()->month;
        $year = now()->year;

        $response = $this->actingAs($admin)
                        ->get(route('payroll.export', [$month, $year]));

        $response->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function employee_cannot_export_payroll()
    {
        $employee = User::factory()->create(['role_id' => $this->employeeRole->id]);
        $month = now()->month;
        $year = now()->year;

        $response = $this->actingAs($employee)
                        ->get(route('payroll.export', [$month, $year]));

        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function guest_cannot_export_payroll()
    {
        $month = now()->month;
        $year = now()->year;

        $response = $this->get(route('payroll.export', [$month, $year]));

        $response->assertRedirect(route('login'));
    }

}
