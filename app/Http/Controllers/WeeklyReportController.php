<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\AttendanceSession;

class WeeklyReportController extends Controller
{
    public function index()
    {
        $sessions = AttendanceSession::orderBy('week_number', 'asc')->get();
        $students = Student::with('attendances')->orderBy('name', 'asc')->get();

        return view('weekly-report', compact('sessions', 'students'));
    }
}
