<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Semester;

class SemesterController extends Controller
{
    public function index(Request $request)
    {
        $semesters = $request->user()->semesters()->withCount('groups')->orderBy('created_at', 'desc')->get();
        return view('dashboard', compact('semesters'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $request->user()->semesters()->create($request->only('name'));
        return redirect()->route('dashboard')->with('success', 'Semester created successfully.');
    }

    public function show(Semester $semester, Request $request)
    {
        abort_if($semester->user_id && $semester->user_id !== $request->user()->id, 403, 'Unauthorized action.');

        $semester->load(['groups' => function($q) {
            $q->withCount('students');
        }]);
        return view('semesters.show', compact('semester'));
    }
}
