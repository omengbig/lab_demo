<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Semester;

class SemesterController extends Controller
{
    public function index()
    {
        $semesters = Semester::withCount('groups')->orderBy('created_at', 'desc')->get();
        return view('dashboard', compact('semesters'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Semester::create($request->only('name'));
        return redirect()->route('dashboard')->with('success', 'Semester created successfully.');
    }

    public function show(Semester $semester)
    {
        $semester->load(['groups' => function($q) {
            $q->withCount('students');
        }]);
        return view('semesters.show', compact('semester'));
    }
}
