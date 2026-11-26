<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class GroupExportController extends Controller
{
    public function excel(Group $group, Request $request)
    {
        abort_if($group->semester->user_id !== $request->user()->id, 403);
        
        $group->load(['attendanceSessions' => fn($q) => $q->orderBy('week_number', 'asc'), 'students.attendances']);
        $sessions = $group->attendanceSessions;
        $students = $group->students->sortBy('name');

        $safeName = \Illuminate\Support\Str::slug($group->name, '_');
        $filename = "{$safeName}_attendance.csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($students, $sessions) {
            $file = fopen('php://output', 'w');
            
            // Write UTF-8 BOM so Excel natively reads the ✓ symbol without encoding issues
            fputs($file, "\xEF\xBB\xBF");
            
            $columns = ['#', 'Matrix Number', 'Name', 'Rate', 'Status'];
            foreach ($sessions as $session) {
                $columns[] = "Week {$session->week_number}";
            }
            fputcsv($file, $columns);

            $counter = 1;
            foreach ($students as $student) {
                // CSV doesn't support background colors, so we visually flag them in text
                $name = $student->is_at_risk ? "⚠️ [AT-RISK] " . $student->name : $student->name;
                
                $row = [
                    $counter++,
                    $student->matrix_number,
                    $name,
                    $student->attendance_rate . '%',
                    $student->is_at_risk ? 'At-Risk' : 'Good Standing'
                ];
                
                foreach ($sessions as $session) {
                    $attendance = $student->attendances->firstWhere('attendance_session_id', $session->id);
                    $status = $attendance ? $attendance->status : 'absent';
                    
                    if ($status === 'present' || $status === 'late') {
                        $row[] = '✓';
                    } else {
                        $row[] = 'X' . ($attendance && $attendance->reason ? " ({$attendance->reason})" : "");
                    }
                }
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function pdf(Group $group, Request $request)
    {
        abort_if($group->semester->user_id !== $request->user()->id, 403);
        
        $group->load(['attendanceSessions' => fn($q) => $q->orderBy('week_number', 'asc'), 'students.attendances']);
        
        $pdf = Pdf::loadView('groups.pdf', [
            'group' => $group,
            'sessions' => $group->attendanceSessions,
            'students' => $group->students->sortBy('name')->values()
        ])->setPaper('a4', 'landscape');
        
        $safeName = \Illuminate\Support\Str::slug($group->name, '_');
        return $pdf->download("{$safeName}_attendance.pdf");
    }
}
