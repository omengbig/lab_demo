<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use App\Models\AttendanceSession;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceImportController extends Controller
{
    public function create(\App\Models\Group $group)
    {
        return view('groups.import', compact('group'));
    }

    public function store(Request $request, \App\Models\Group $group, \App\Services\PdfAttendanceParser $parser)
    {
        // 1. Validate the Request
        $request->validate([
            'week_number' => 'required|integer|min:1|max:52',
            'session_date' => 'required|date',
            'pdf_file' => 'required|file|mimes:pdf|max:5120', // Max 5MB
        ]);

        try {
            DB::transaction(function () use ($request, $group, $parser) {
                // 2. Create the AttendanceSession tied to the Group
                $session = AttendanceSession::create([
                    'group_id' => $group->id,
                    'created_by_user_id' => auth()->id(),
                    'week_number' => $request->week_number,
                    'session_date' => $request->session_date,
                ]);

                // 3. Parse the PDF
                $text = $parser->extractText($request->file('pdf_file')->getPathname());

                // 4. Data Normalization & Regex
                $lines = explode("\n", $text);
                $pattern = '/^(\d+)\s+([A-Za-z0-9]+)\s+(.*?)(?:\s+\d{2}:\d{2}:\d{2}){0,2}$/';
                
                $parsedRecords = 0;

                foreach ($lines as $line) {
                    $line = trim($line);
                    if (preg_match($pattern, $line, $matches)) {
                        $matrixNumber = $matches[2];
                        $name = trim($matches[3]);

                        // Determine status based on missing time punches (simplistic example logic)
                        $status = 'present';
                        if (str_contains(strtolower($line), 'absent')) {
                            $status = 'absent';
                        } elseif (str_contains(strtolower($line), 'late')) {
                            $status = 'late';
                        }

                        // 5. Find existing student in the group's roster
                        $student = Student::where('matrix_number', $matrixNumber)
                                          ->where('group_id', $group->id)
                                          ->first();

                        if (!$student) {
                            // If student is not registered in the Master Roster, skip them!
                            continue;
                        }

                        // 6. create Attendance
                        Attendance::updateOrCreate(
                            [
                                'student_id' => $student->id,
                                'attendance_session_id' => $session->id,
                            ],
                            [
                                'status' => $status
                            ]
                        );

                        $parsedRecords++;
                    }
                }

                if ($parsedRecords === 0) {
                    throw new \Exception("No valid attendance records found in the PDF. Please check the format.");
                }
            });

            return redirect()->route('groups.show', $group)->with('success', 'Attendance imported successfully for this group.');

        } catch (\Exception $e) {
            Log::error('Attendance Import Failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
}
