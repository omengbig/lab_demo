<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\AttendanceSession;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    public function updateReason(Request $request, Student $student, AttendanceSession $session)
    {
        $request->validate(['reason' => 'nullable|string|max:255']);
        
        // Ensure student belongs to a group that the user owns
        abort_if($student->group->semester->user_id !== $request->user()->id, 403);
        
        Attendance::updateOrCreate(
            ['student_id' => $student->id, 'attendance_session_id' => $session->id],
            ['status' => 'absent', 'reason' => $request->reason]
        );

        return back()->with('success', 'Reason updated successfully.');
    }
}
