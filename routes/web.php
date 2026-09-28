<?php

use App\Http\Controllers\AdminCmsController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdmissionsController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampusController;
use App\Http\Controllers\CourseRegistrationController;
use App\Http\Controllers\DimCandidateController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\GradeDistributionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LmsController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ResearchController;
use App\Http\Controllers\StudentServicesController;
use App\Http\Controllers\TranscriptController;
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

// Public E-Verification Portal
Route::get('/transcript/verify/{code}', [TranscriptController::class, 'verifyCode'])->name('transcript.verify');

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
    Route::get('/registration', [CourseRegistrationController::class, 'index'])->name('registration.index');
    Route::post('/registration/enroll/{sectionId}', [CourseRegistrationController::class, 'enroll'])->name('registration.enroll');
    Route::post('/registration/drop/{enrollmentId}', [CourseRegistrationController::class, 'drop'])->name('registration.drop');

    // Official Academic Transcript & Degree Audit
    Route::get('/transcript/{studentId?}', [TranscriptController::class, 'show'])->name('transcript');

    // E-Arayış Official Digital Certificate Generator
    Route::get('/e-arayis', [StudentServicesController::class, 'generateCertificate'])->name('certificate.generate');

    // Per-Session Attendance Recording (Teachers & Admins)
    Route::post('/attendance/sections/{sectionId}', [AttendanceController::class, 'storeSession'])
        ->middleware('role:teacher,admin,system_admin')
        ->name('attendance.store');

    // LMS Interactive Classroom (Courses, Assignments, Submissions, Materials)
    Route::get('/lms/courses/{sectionId}', [LmsController::class, 'showCourse'])->name('lms.course');
    Route::post('/lms/sections/{sectionId}/assignments', [LmsController::class, 'createAssignment'])->name('lms.assignment.create');
    Route::post('/lms/assignments/{assignmentId}/submit', [LmsController::class, 'submitAssignment'])->name('lms.assignment.submit');
    Route::post('/lms/submissions/{submissionId}/grade', [LmsController::class, 'gradeSubmission'])->name('lms.submission.grade');
    Route::post('/lms/sections/{sectionId}/materials', [LmsController::class, 'addMaterial'])->name('lms.material.add');
    Route::post('/lms/sections/{sectionId}/announcements', [LmsController::class, 'addAnnouncement'])->name('lms.announcement.add');

    // Financial Portal, Online Payment Gateway & Receipts
    Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
    Route::post('/finance/invoices/{invoiceId}/pay', [FinanceController::class, 'payOnline'])->name('finance.pay');
    Route::post('/finance/invoices/{invoiceId}/installments', [FinanceController::class, 'createInstallments'])->name('finance.installments');
    Route::get('/finance/invoices/{invoiceId}/receipt', [FinanceController::class, 'receipt'])->name('finance.receipt');

    // In-App Notification Center
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

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
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs');

        // CMS & Academic Management
        Route::get('/cms', [AdminCmsController::class, 'index'])->name('cms.index');
        Route::post('/cms/news', [AdminCmsController::class, 'storeNews'])->name('cms.news');
        Route::post('/cms/events', [AdminCmsController::class, 'storeEvent'])->name('cms.events');
        Route::post('/cms/courses', [AdminCmsController::class, 'storeCourse'])->name('cms.courses');
        Route::post('/cms/sections', [AdminCmsController::class, 'storeSection'])->name('cms.sections');
        Route::post('/cms/terms/{termId}/toggle', [AdminCmsController::class, 'toggleRegistration'])->name('cms.terms.toggle');
        Route::post('/cms/terms/{termId}/current', [AdminCmsController::class, 'setCurrentTerm'])->name('cms.terms.set_current');
    });

    // Convenient Aliases
    Route::get('/transcript', [TranscriptController::class, 'show'])->name('transcript.index');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark_all_read');
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark_read');

    // Student Services (Matches Student Dropdown Menu & Academic Portal)
    Route::get('/semester-grades', [StudentServicesController::class, 'grades'])->name('semester-grades');
    Route::get('/schedule', [StudentServicesController::class, 'schedule'])->name('schedule');
    Route::get('/attendance-report', [StudentServicesController::class, 'attendance'])->name('attendance-report');
    Route::get('/appeals', [StudentServicesController::class, 'appeals'])->name('appeals.index');
    Route::post('/appeals', [StudentServicesController::class, 'storeAppeal'])->name('appeals.store');

    // Teacher Grade Component Distribution & Electronic Student Journal
    Route::middleware('role:teacher,admin,system_admin')->group(function () {
        Route::post('/grades/{id}', [PortalController::class, 'updateGrade'])->name('grades.update');
        Route::get('/grading/{sectionId}', [GradeDistributionController::class, 'showCourseGrading'])->name('grading.show');
        Route::post('/grading/{sectionId}/components', [GradeDistributionController::class, 'saveComponents'])->name('grading.components.save');
        Route::post('/grading/{sectionId}/template', [GradeDistributionController::class, 'applyDefaultTemplate'])->name('grading.components.template');
        Route::post('/grading/{sectionId}/scores', [GradeDistributionController::class, 'saveStudentScores'])->name('grading.scores.save');

        // Review student appeals
        Route::get('/admin/appeals', [StudentServicesController::class, 'manageAppeals'])->name('admin.appeals.index');
        Route::post('/admin/appeals/{id}/respond', [StudentServicesController::class, 'respondAppeal'])->name('admin.appeals.respond');
    });

    // Admissions status mutation (protected by role middleware & policy)
    Route::post('/applications/{id}/status', [PortalController::class, 'updateApplicationStatus'])
        ->middleware('role:admin,system_admin')
        ->name('applications.status');
});
