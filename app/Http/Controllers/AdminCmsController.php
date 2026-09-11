<?php

namespace App\Http\Controllers;

use App\Models\AcademicTerm;
use App\Models\ActivityLog;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Department;
use App\Models\Event;
use App\Models\Faculty;
use App\Models\NewsArticle;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCmsController extends Controller
{
    /**
     * CMS Overview dashboard.
     */
    public function index()
    {
        $news = NewsArticle::latest()->take(10)->get();
        $events = Event::latest()->take(10)->get();
        $programs = Program::with('department.faculty')->get();
        $courses = Course::with(['program', 'prerequisite'])->get();
        $terms = AcademicTerm::all();
        $faculties = Faculty::all();
        $teachers = User::where('role', 'teacher')->get();

        return view('portal.admin_cms', compact('news', 'events', 'programs', 'courses', 'terms', 'faculties', 'teachers'));
    }

    /**
     * Create news article.
     */
    public function storeNews(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'summary' => 'nullable|string',
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'body' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
        ]);

        $summary = $validated['summary'] ?? $validated['excerpt'] ?? Str::limit(strip_tags($validated['content'] ?? $validated['body'] ?? ''), 150);
        $content = $validated['content'] ?? $validated['body'] ?? $summary;

        $article = NewsArticle::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . rand(100, 999),
            'category' => $validated['category'],
            'summary' => $summary,
            'content' => $content,
            'is_featured' => $request->boolean('is_featured'),
            'published_at' => $request->input('published_at') ? now()->parse($request->published_at) : now(),
        ]);

        ActivityLog::log('CMS_NEWS_CREATED', "Yeni xəbər yaradıldı: {$article->title}", $article);

        return back()->with('success', "Xəbər ('{$article->title}') uğurla dərc edildi.");
    }

    /**
     * Create event.
     */
    public function storeEvent(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'description' => 'required|string',
            'speaker_names' => 'nullable|string',
            'venue' => 'nullable|string',
            'location' => 'nullable|string',
            'start_time' => 'nullable|date',
            'starts_at' => 'nullable|date',
            'end_time' => 'nullable|date',
            'ends_at' => 'nullable|date',
            'capacity' => 'nullable|integer|min:1',
        ]);

        $venue = $validated['venue'] ?? $validated['location'] ?? 'Neftçilər Korpusu';
        $startTime = $validated['start_time'] ?? $validated['starts_at'] ?? now()->addDays(7);
        $endTime = $validated['end_time'] ?? $validated['ends_at'] ?? now()->addDays(7)->addHours(2);
        $capacity = $validated['capacity'] ?? 100;

        $event = Event::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . rand(100, 999),
            'category' => $validated['category'],
            'description' => $validated['description'],
            'speaker_names' => $validated['speaker_names'] ?? null,
            'venue' => $venue,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'capacity' => $capacity,
            'is_featured' => true,
        ]);

        ActivityLog::log('CMS_EVENT_CREATED', "Yeni tədbir yaradıldı: {$event->title}", $event);

        return back()->with('success', "Tədbir ('{$event->title}') uğurla yaradıldı.");
    }

    /**
     * Create Course.
     */
    public function storeCourse(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'prerequisite_course_id' => 'nullable|exists:courses,id',
            'code' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'credits' => 'required|integer|min:1|max:30',
            'semester' => 'required|integer|min:1|max:8',
            'type' => 'required|in:compulsory,elective',
            'syllabus' => 'nullable|string',
        ]);

        $course = Course::create($validated);
        ActivityLog::log('CMS_COURSE_CREATED', "Yeni fənn yaradıldı: {$course->name} ({$course->code})", $course);

        return back()->with('success', "Fənn ('{$course->name}') uğurla əlavə edildi.");
    }

    /**
     * Create Course Section (Qrup).
     */
    public function storeSection(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:users,id',
            'semester_term' => 'required|string',
            'room_number' => 'required|string',
            'day_of_week' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required',
            'capacity' => 'required|integer|min:5|max:100',
        ]);

        $section = CourseSection::create($validated);
        ActivityLog::log('CMS_SECTION_CREATED', "Yeni dərs qrupu yaradıldı: {$section->room_number}", $section);

        return back()->with('success', 'Dərs qrupu (Course Section) uğurla yaradıldı.');
    }

    /**
     * Toggle Academic Term Registration Window.
     */
    public function toggleRegistration(Request $request, $termId)
    {
        $term = AcademicTerm::findOrFail($termId);
        $term->registration_open = ! $term->registration_open;
        $term->save();

        $status = $term->registration_open ? 'AÇIQ' : 'BAĞLI';
        ActivityLog::log('ACADEMIC_TERM_UPDATED', "{$term->name} üçün fənn qeydiyyatı pəncərəsi {$status} edildi.", $term);

        return back()->with('success', "{$term->name} üçün fənn qeydiyyatı pəncərəsi {$status} edildi.");
    }

    /**
     * Set term as current active semester.
     */
    public function setCurrentTerm(Request $request, $termId)
    {
        AcademicTerm::where('is_current', true)->update(['is_current' => false]);
        $term = AcademicTerm::findOrFail($termId);
        $term->is_current = true;
        $term->save();

        ActivityLog::log('ACADEMIC_TERM_CURRENT_CHANGED', "{$term->name} cari semestr olaraq təyin edildi.", $term);

        return back()->with('success', "{$term->name} cari semestr olaraq təyin edildi.");
    }
}
