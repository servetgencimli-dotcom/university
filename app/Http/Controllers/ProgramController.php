<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $query = Program::with('department.faculty', 'courses');

        if ($request->filled('degree')) {
            $query->where('degree_level', $request->degree);
        }

        if ($request->filled('faculty_id')) {
            $query->whereHas('department', function ($q) use ($request) {
                $q->where('faculty_id', $request->faculty_id);
            });
        }

        if ($request->filled('language')) {
            $query->where('language', 'like', "%{$request->language}%");
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('overview', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $programs = $query->paginate(12)->withQueryString();
        $faculties = Faculty::all();

        return view('pages.programs.index', compact('programs', 'faculties'));
    }

    public function show($slug)
    {
        $program = Program::with(['department.faculty', 'courses.sections.teacher'])
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedPrograms = Program::where('department_id', $program->department_id)
            ->where('id', '!=', $program->id)
            ->take(3)
            ->get();

        return view('pages.programs.show', compact('program', 'relatedPrograms'));
    }
}
