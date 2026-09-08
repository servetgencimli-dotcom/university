<?php

namespace App\Http\Controllers;

use App\Models\Faculty;

class FacultyController extends Controller
{
    public function index()
    {
        $faculties = Faculty::with(['departments.programs', 'professors'])->get();

        return view('pages.faculties.index', compact('faculties'));
    }

    public function show($slug)
    {
        $faculty = Faculty::with(['departments.programs', 'professors'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('pages.faculties.show', compact('faculty'));
    }
}
