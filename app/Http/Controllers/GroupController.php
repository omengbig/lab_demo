<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Semester;

class GroupController extends Controller
{
    public function store(Request $request, Semester $semester)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $group = $semester->groups()->create($request->only('name'));
        return redirect()->route('groups.show', $group)->with('success', 'Group created successfully! You can now import your Master Roster here.');
    }

    public function show(Group $group, Request $request)
    {
        // Eager load everything needed for the grid and the stats
        $group->load([
            'attendanceSessions' => function($q) {
                $q->orderBy('week_number', 'asc');
            },
            'students.attendances'
        ]);

        $sessions = $group->attendanceSessions;
        $students = $group->students;

        // Sorting
        $sort = $request->query('sort', 'name-asc');
        if ($sort === 'name-asc') {
            $students = $students->sortBy('name');
        } elseif ($sort === 'name-desc') {
            $students = $students->sortByDesc('name');
        }

        // Filtering by Status
        $filter = $request->query('filter');
        if ($filter === 'at-risk') {
            $students = $students->filter(fn($student) => $student->is_at_risk);
        } elseif ($filter === 'good-standing') {
            $students = $students->filter(fn($student) => !$student->is_at_risk);
        }

        // Chart Data Calculation
        $totalSessions = $sessions->count();
        $totalStudentsForChart = $group->students->count();
        $totalPossible = $totalSessions * $totalStudentsForChart;
        
        $totalPresent = $group->students->sum(function($student) {
            return $student->attendances->whereIn('status', ['present', 'late'])->count();
        });
        $totalAbsent = $totalPossible > 0 ? ($totalPossible - $totalPresent) : 0;

        return view('groups.show', compact('group', 'sessions', 'students', 'totalPresent', 'totalAbsent'));
    }

    public function update(Group $group, Request $request)
    {
        abort_if($group->semester->user_id !== $request->user()->id, 403);
        $request->validate(['name' => 'required|string|max:255']);
        $group->update($request->only('name'));
        return back()->with('success', 'Group name updated successfully.');
    }

    public function destroy(Group $group, Request $request)
    {
        abort_if($group->semester->user_id !== $request->user()->id, 403);
        $group->delete();
        return back()->with('success', 'Group deleted successfully.');
    }
}
