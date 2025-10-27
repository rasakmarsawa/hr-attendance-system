<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\PayrollController;
use Illuminate\Support\Collection;

class PayrollControllerUnitTest extends TestCase
{
    public function test_calculate_payroll_returns_correct_total()
    {
        $controller = new PayrollController();

        $counts = [
            'present' => 2,
            'absent'  => 1,
            'late'    => 1,
        ];

        $result = $this->invokeMethod($controller, 'calculatePayroll', [100, $counts]);

        $this->assertEquals(300, $result); // 2 present + 1 late = 3 × 100
    }

    private function invokeMethod($object, string $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass($object);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
?>