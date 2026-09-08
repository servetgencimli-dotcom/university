<?php

namespace App\Http\Controllers;

use App\Models\CampusBuilding;
use App\Models\Event;
use App\Models\Faculty;
use App\Models\NewsArticle;
use App\Models\Program;
use App\Models\ResearchPaper;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $faculties = Faculty::with('departments')->orderBy('established_year')->get();
        $popularPrograms = Program::with('department.faculty')->where('is_popular', true)->take(6)->get();
        $featuredEvents = Event::where('start_time', '>=', now())->orderBy('start_time')->take(3)->get();
        if ($featuredEvents->isEmpty()) {
            $featuredEvents = Event::orderBy('start_time', 'desc')->take(3)->get();
        }
        $featuredNews = NewsArticle::orderBy('published_at', 'desc')->take(3)->get();
        $campusBuildings = CampusBuilding::all();
        $featuredResearch = ResearchPaper::with('author')->where('is_featured', true)->take(2)->get();

        $stats = [
            'students_count' => User::whereIn('role', ['student', 'master_student'])->count() + 5120,
            'professors_count' => User::where('role', 'teacher')->count() + 240,
            'programs_count' => Program::count(),
            'employment_rate' => 96.8,
            'research_patents' => 48,
            'global_rank' => 'Top 450 (QS)',
        ];

        return view('pages.home', compact(
            'faculties',
            'popularPrograms',
            'featuredEvents',
            'featuredNews',
            'campusBuildings',
            'featuredResearch',
            'stats'
        ));
    }
}
