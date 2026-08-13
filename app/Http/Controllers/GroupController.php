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
        $semester->groups()->create($request->only('name'));
        return redirect()->route('semesters.show', $semester)->with('success', 'Group created successfully.');
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

        return view('groups.show', compact('group', 'sessions', 'students'));
    }
}
