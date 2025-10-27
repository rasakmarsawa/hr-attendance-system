<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\AttendanceController;
use Carbon\Carbon;

class AttendanceControllerUnitTest extends TestCase
{
    public function test_checkin_status_before_cutoff()
    {
        $controller = new AttendanceController();
        $time = Carbon::createFromTime(9, 30); // before 10 AM
        $status = $controller->determineStatus($time);

        $this->assertEquals('present', $status);
    }

    public function test_checkin_status_after_cutoff()
    {
        $controller = new AttendanceController();
        $time = Carbon::createFromTime(10, 30); // after 10 AM
        $status = $controller->determineStatus($time);

        $this->assertEquals('late', $status);
    }
}
?>