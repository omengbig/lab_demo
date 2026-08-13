<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Student;
use App\Models\AttendanceSession;
use App\Models\Attendance;

class AttendanceCalculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_with_100_percent_attendance()
    {
        $student = Student::factory()->create();
        $sessions = AttendanceSession::factory()->count(5)->create();
        
        foreach ($sessions as $session) {
            Attendance::factory()->create([
                'student_id' => $student->id,
                'attendance_session_id' => $session->id,
                'status' => 'present'
            ]);
        }

        $this->assertEquals(100.0, $student->attendance_rate);
        $this->assertFalse($student->is_at_risk);
    }

    public function test_student_with_0_percent_attendance()
    {
        $student = Student::factory()->create();
        $sessions = AttendanceSession::factory()->count(5)->create();
        
        foreach ($sessions as $session) {
            Attendance::factory()->create([
                'student_id' => $student->id,
                'attendance_session_id' => $session->id,
                'status' => 'absent'
            ]);
        }

        $this->assertEquals(0.0, $student->attendance_rate);
        $this->assertTrue($student->is_at_risk);
    }

    public function test_student_exactly_on_80_percent_boundary()
    {
        $student = Student::factory()->create();
        $sessions = AttendanceSession::factory()->count(5)->create(); // 5 total sessions
        
        // 4 present (80%), 1 absent
        foreach ($sessions->take(4) as $session) {
            Attendance::factory()->create(['student_id' => $student->id, 'attendance_session_id' => $session->id, 'status' => 'present']);
        }
        Attendance::factory()->create(['student_id' => $student->id, 'attendance_session_id' => $sessions->last()->id, 'status' => 'absent']);

        $this->assertEquals(80.0, $student->attendance_rate);
        $this->assertFalse($student->is_at_risk); // < 80.0 is at risk, so 80.0 is safe
    }

    public function test_student_absent_for_2_consecutive_weeks_but_overall_attendance_above_80()
    {
        config(['attendance.consecutive_absence_limit' => 2]);
        
        $student = Student::factory()->create();
        
        // Let's make 10 sessions total. 8 present, 2 absent at the very end.
        // Overall rate = 80%.
        $sessions = AttendanceSession::factory()->count(10)->create();
        
        foreach ($sessions->take(8) as $session) {
            Attendance::factory()->create(['student_id' => $student->id, 'attendance_session_id' => $session->id, 'status' => 'present']);
        }
        
        // Last 2 sessions are absent (consecutive)
        $lastTwo = $sessions->slice(8, 2);
        foreach ($lastTwo as $session) {
            Attendance::factory()->create(['student_id' => $student->id, 'attendance_session_id' => $session->id, 'status' => 'absent']);
        }

        $this->assertEquals(80.0, $student->attendance_rate);
        $this->assertTrue($student->is_at_risk); // Flagged because of consecutive absences
    }
}
