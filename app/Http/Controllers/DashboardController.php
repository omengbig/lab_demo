<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\AttendanceSession;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Eager load attendances to prevent N+1 queries during rate calculation
        $students = Student::with('attendances')->get();

        // 1. Filtering
        if ($request->query('filter') === 'at-risk') {
            $students = $students->filter(function ($student) {
                return $student->is_at_risk;
            });
        }

        // 2. Sorting
        $sort = $request->query('sort', 'rate-asc'); // Default sort lowest rate first
        if ($sort === 'rate-asc') {
            $students = $students->sortBy('attendance_rate')->values();
        } elseif ($sort === 'rate-desc') {
            $students = $students->sortByDesc('attendance_rate')->values();
        } else {
            $students = $students->sortBy('name')->values();
        }

        // 3. Fetch latest session for metadata banner
        $latestSession = AttendanceSession::with('creator')->latest('created_at')->first();

        return view('dashboard', compact('students', 'latestSession'));
    }
}
