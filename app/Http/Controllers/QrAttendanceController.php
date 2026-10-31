<?php

namespace App\Http\Controllers;

use App\Models\AttendanceQrSession;
use App\Models\AttendanceRecord;
use App\Models\CourseSection;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QrAttendanceController extends Controller
{
    /**
     * Teacher QR Attendance Kiosk view
     */
    public function kiosk($sectionId)
    {
        $section = CourseSection::with(['course', 'enrollments.student'])->findOrFail($sectionId);

        // Security check
        if (Auth::user()->cannot('kiosk', $section)) {
            abort(403, 'Siz yalnız öz dərsiniz üçün QR Kiosk aça bilərsiniz.');
        }

        // Get current active session or create new
        $session = AttendanceQrSession::where('course_section_id', $section->id)
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $session) {
            $session = AttendanceQrSession::create([
                'course_section_id' => $section->id,
                'teacher_id' => Auth::id(),
                'session_code' => bin2hex(random_bytes(16)),
                'session_date' => now()->toDateString(),
                'expires_at' => now()->addMinutes(20),
                'is_active' => true,
            ]);
        }

        // Fetch present student IDs for this section & date
        $enrollmentIds = $section->enrollments->pluck('id');
        $presentEnrollmentIds = AttendanceRecord::whereIn('enrollment_id', $enrollmentIds)
            ->whereDate('session_date', $session->session_date->toDateString())
            ->where('status', 'present')
            ->pluck('enrollment_id')
            ->toArray();

        return view('portal.qr_kiosk', compact('section', 'session', 'presentEnrollmentIds'));
    }

    /**
     * Teacher: Refresh / regenerate new session code
     */
    public function createSession(Request $request, $sectionId)
    {
        $section = CourseSection::findOrFail($sectionId);

        if (Auth::user()->cannot('kiosk', $section)) {
            abort(403);
        }

        // Deactivate old active sessions
        AttendanceQrSession::where('course_section_id', $section->id)
            ->update(['is_active' => false]);

        // Create new active session with 15 minutes TTL
        $session = AttendanceQrSession::create([
            'course_section_id' => $section->id,
            'teacher_id' => Auth::id(),
            'session_code' => bin2hex(random_bytes(16)),
            'session_date' => now()->toDateString(),
            'expires_at' => now()->addMinutes(15),
            'is_active' => true,
        ]);

        return redirect()->route('portal.qr.kiosk', $section->id)->with('success', 'Yeni dinamik QR kod generasiya edildi.');
    }

    /**
     * JSON Endpoint: Live status polling for Kiosk view
     */
    public function liveStatus($sessionId)
    {
        $session = AttendanceQrSession::with('courseSection.enrollments.student')->findOrFail($sessionId);
        $user = Auth::user();

        if ($user->cannot('kiosk', $session->courseSection)) {
            abort(403, 'Bu QR sessiyasının canlı statusuna baxış icazəniz yoxdur.');
        }

        $enrollments = $session->courseSection->enrollments;
        $enrollmentIds = $enrollments->pluck('id');

        $presentEnrollments = AttendanceRecord::whereIn('enrollment_id', $enrollmentIds)
            ->whereDate('session_date', $session->session_date->toDateString())
            ->where('status', 'present')
            ->pluck('enrollment_id')
            ->toArray();

        $students = $enrollments->filter(fn ($enrollment) => $enrollment->student)->values()->map(function ($enrollment) use ($presentEnrollments) {
            return [
                'id' => $enrollment->student->id,
                'enrollment_id' => $enrollment->id,
                'name' => $enrollment->student->name,
                'email' => $enrollment->student->email,
                'is_present' => in_array($enrollment->id, $presentEnrollments),
            ];
        });

        // Rotating token every 30 seconds for security
        if ($session->updated_at && $session->updated_at->diffInSeconds(now()) >= 30) {
            $session->update(['session_code' => bin2hex(random_bytes(16))]);
        }

        return response()->json([
            'session_code' => $session->session_code,
            'scan_url' => route('portal.qr.scan', $session->session_code),
            'is_active' => $session->is_active && $session->expires_at->isFuture(),
            'expires_at' => $session->expires_at->toIso8601String(),
            'total_students' => $enrollments->count(),
            'present_count' => count($presentEnrollments),
            'students' => $students,
        ]);
    }

    /**
     * Student: Landing view when scanning QR code
     */
    public function scanCheckin($code)
    {
        $session = AttendanceQrSession::where('session_code', $code)
            ->with(['courseSection.course', 'teacher'])
            ->first();

        if (! $session || ! $session->is_active || $session->expires_at->isPast()) {
            return view('portal.qr_scan_checkin', [
                'expired' => true,
                'message' => 'QR kodun vaxtı bitib və ya sessiya deaktivdir. Lütfən ekrandakı yeni QR kodu yenidən skan edin.',
            ]);
        }

        $user = Auth::user();

        // Check if user is enrolled in this section
        $enrollment = Enrollment::where('course_section_id', $session->course_section_id)
            ->where('student_id', $user->id)
            ->first();

        if (! $enrollment) {
            return view('portal.qr_scan_checkin', [
                'expired' => false,
                'error' => 'Siz bu qrupa qeydiyyatdan keçməmisiniz.',
                'session' => $session,
            ]);
        }

        // Check if already checked in
        $alreadyPresent = AttendanceRecord::where('enrollment_id', $enrollment->id)
            ->whereDate('session_date', $session->session_date->toDateString())
            ->where('status', 'present')
            ->exists();

        $expired = false;

        return view('portal.qr_scan_checkin', compact('session', 'enrollment', 'alreadyPresent', 'expired'));
    }

    /**
     * Student: Process checkin submission
     */
    public function processCheckin(Request $request, $code)
    {
        $session = AttendanceQrSession::where('session_code', $code)->firstOrFail();

        if (! $session->is_active || $session->expires_at->isPast()) {
            return redirect()->back()->with('error', 'Sessiyanın vaxtı bitib.');
        }

        $user = Auth::user();
        $enrollment = Enrollment::where('course_section_id', $session->course_section_id)
            ->where('student_id', $user->id)
            ->firstOrFail();

        // Mark present
        AttendanceRecord::updateOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'session_date' => $session->session_date->toDateString(),
            ],
            [
                'status' => 'present',
                'notes' => 'QR Kiosk vasitəsilə canlı istirak',
            ]
        );

        // Recalculate student attendance percentage
        $enrollment->recalculateAttendancePercentage();

        return redirect()->route('portal.qr.scan', $code)->with('success', 'Davamiyyətiniz uğurla qeydə alındı! 🟢');
    }
}
