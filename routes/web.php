<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdmissionsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampusController;
use App\Http\Controllers\DimCandidateController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ResearchController;
use Illuminate\Support\Facades\Route;

// Public Website Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Academic Programs
Route::get('/programs', [ProgramController::class, 'index'])->name('programs.index');
Route::get('/programs/{slug}', [ProgramController::class, 'show'])->name('programs.show');

// Faculties & Departments (Schools)
Route::get('/faculties', [FacultyController::class, 'index'])->name('faculties.index');
Route::get('/faculties/{slug}', [FacultyController::class, 'show'])->name('faculties.show');

// Admissions & Application Tracking
Route::get('/admissions/apply', [AdmissionsController::class, 'applyForm'])->name('admissions.apply');
Route::post('/admissions/apply', [AdmissionsController::class, 'submitApplication'])->name('admissions.submit');
Route::get('/admissions/track', [AdmissionsController::class, 'track'])->name('admissions.track');

// Virtual Campus Map
Route::get('/campus-map', [CampusController::class, 'index'])->name('campus.map');

// Research & Innovations
Route::get('/research', [ResearchController::class, 'index'])->name('research.index');

// Events & Conferences
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{slug}', [EventController::class, 'show'])->name('events.show');
Route::post('/events/{slug}/rsvp', [EventController::class, 'rsvp'])->name('events.rsvp');

// News & Articles
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Role-Based Portals (Protected with Auth and Role Middleware)
Route::middleware('auth')->prefix('portal')->name('portal.')->group(function () {
    Route::get('/', [PortalController::class, 'dashboard'])->name('dashboard');

    // Impersonation exit (accessible to any logged in user who has impersonator_id in session)
    Route::post('/admin/stop-impersonating', [AdminUserController::class, 'stopImpersonating'])
        ->name('admin.stop-impersonating');

    // Self-Service Course Registration (Students & Master Students)
    Route::get('/registration', [\App\Http\Controllers\CourseRegistrationController::class, 'index'])->name('registration.index');
    Route::post('/registration/enroll/{sectionId}', [\App\Http\Controllers\CourseRegistrationController::class, 'enroll'])->name('registration.enroll');
    Route::post('/registration/drop/{enrollmentId}', [\App\Http\Controllers\CourseRegistrationController::class, 'drop'])->name('registration.drop');

    // Official Academic Transcript & Degree Audit
    Route::get('/transcript/{studentId?}', [\App\Http\Controllers\TranscriptController::class, 'show'])->name('transcript');

    // Per-Session Attendance Recording (Teachers & Admins)
    Route::post('/attendance/sections/{sectionId}', [\App\Http\Controllers\AttendanceController::class, 'storeSession'])
        ->middleware('role:teacher,admin,system_admin')
        ->name('attendance.store');

    // LMS Interactive Classroom (Courses, Assignments, Submissions, Materials)
    Route::get('/lms/courses/{sectionId}', [\App\Http\Controllers\LmsController::class, 'showCourse'])->name('lms.course');
    Route::post('/lms/sections/{sectionId}/assignments', [\App\Http\Controllers\LmsController::class, 'createAssignment'])->name('lms.assignment.create');
    Route::post('/lms/assignments/{assignmentId}/submit', [\App\Http\Controllers\LmsController::class, 'submitAssignment'])->name('lms.assignment.submit');
    Route::post('/lms/submissions/{submissionId}/grade', [\App\Http\Controllers\LmsController::class, 'gradeSubmission'])->name('lms.submission.grade');
    Route::post('/lms/sections/{sectionId}/materials', [\App\Http\Controllers\LmsController::class, 'addMaterial'])->name('lms.material.add');
    Route::post('/lms/sections/{sectionId}/announcements', [\App\Http\Controllers\LmsController::class, 'addAnnouncement'])->name('lms.announcement.add');

    // Financial Portal, Online Payment Gateway & Receipts
    Route::get('/finance', [\App\Http\Controllers\FinanceController::class, 'index'])->name('finance.index');
    Route::post('/finance/invoices/{invoiceId}/pay', [\App\Http\Controllers\FinanceController::class, 'payOnline'])->name('finance.pay');
    Route::get('/finance/invoices/{invoiceId}/receipt', [\App\Http\Controllers\FinanceController::class, 'receipt'])->name('finance.receipt');

    // In-App Notification Center
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    // System Admin Full User, RBAC, DIM & CMS Management
    Route::middleware('role:system_admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::post('/users/{id}/password', [AdminUserController::class, 'updatePassword'])->name('users.password');
        Route::post('/users/{id}/permissions', [AdminUserController::class, 'updatePermissions'])->name('users.permissions');
        Route::post('/impersonate/{id}', [AdminUserController::class, 'impersonate'])->name('impersonate');

        // DIM Candidate Admissions Registry
        Route::get('/dim-candidates', [DimCandidateController::class, 'index'])->name('dim.index');
        Route::post('/dim-candidates', [DimCandidateController::class, 'store'])->name('dim.store');
        Route::post('/dim-candidates/{id}/accept', [DimCandidateController::class, 'acceptAsStudent'])->name('dim.accept');
        Route::post('/dim-candidates/{id}/reject', [DimCandidateController::class, 'reject'])->name('dim.reject');

        // Audit Logs
        Route::get('/audit-logs', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit-logs');

        // CMS & Academic Management
        Route::get('/cms', [\App\Http\Controllers\AdminCmsController::class, 'index'])->name('cms.index');
        Route::post('/cms/news', [\App\Http\Controllers\AdminCmsController::class, 'storeNews'])->name('cms.news');
        Route::post('/cms/events', [\App\Http\Controllers\AdminCmsController::class, 'storeEvent'])->name('cms.events');
        Route::post('/cms/courses', [\App\Http\Controllers\AdminCmsController::class, 'storeCourse'])->name('cms.courses');
        Route::post('/cms/sections', [\App\Http\Controllers\AdminCmsController::class, 'storeSection'])->name('cms.sections');
        Route::post('/cms/terms/{termId}/toggle', [\App\Http\Controllers\AdminCmsController::class, 'toggleRegistration'])->name('cms.terms.toggle');
    });

    // Teacher & Admin grading mutation (protected by role middleware & policy)
    Route::post('/grades/{id}', [PortalController::class, 'updateGrade'])
        ->middleware('role:teacher,admin,system_admin')
        ->name('grades.update');

    // Admissions status mutation (protected by role middleware & policy)
    Route::post('/applications/{id}/status', [PortalController::class, 'updateApplicationStatus'])
        ->middleware('role:admin,system_admin')
        ->name('applications.status');
});
