<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TranscriptController extends Controller
{
    /**
     * Display student's official academic transcript and degree audit.
     */
    public function show(Request $request, $studentId = null)
    {
        $currentUser = Auth::user();

        if ($studentId && ($currentUser->isAdmin() || $currentUser->isTeacher())) {
            $student = User::with(['faculty', 'department'])->findOrFail($studentId);
        } else {
            $student = $currentUser;
        }

        // Financial Debt check for official transcript
        $hasDebt = $student->hasUnpaidDebt();

        $enrollments = Enrollment::with(['courseSection.course.program', 'courseSection.teacher'])
            ->where('student_id', $student->id)
            ->get();

        // Group by semester term
        $termsHistory = $enrollments->groupBy(fn ($e) => $e->courseSection?->semester_term ?? 'Digər');

        $requiredCredits = $student->degree_level === 'master' ? 120 : 240;
        $earnedCredits = $student->earned_credits ?? 0;
        $degreeProgressPercent = min(100, round(($earnedCredits / $requiredCredits) * 100, 1));

        return view('portal.transcript', compact(
            'student',
            'hasDebt',
            'termsHistory',
            'requiredCredits',
            'earnedCredits',
            'degreeProgressPercent'
        ));
    }

    /**
     * Public QR verification portal for external entities (Employers, Embassies, Universities).
     */
    public function verifyCode(Request $request, $code)
    {
        $student = User::with(['faculty', 'department', 'enrollments.courseSection.course'])
            ->get()
            ->first(fn ($u) => $u->getVerificationHash() === strtoupper($code) || $u->student_id === $code);

        if (! $student) {
            return view('portal.transcript_verify', [
                'isValid' => false,
                'code' => $code,
                'message' => 'Doğrulama Kodu Tapılmadı! Sənəd etibarsızdır və ya rəsmi bazada qeydə alınmayıb.',
            ]);
        }

        $enrollments = $student->enrollments;
        $earnedCredits = $student->earned_credits ?? 0;
        $cGPA = $student->gpa ?? 0.00;

        return view('portal.transcript_verify', [
            'isValid' => true,
            'code' => $code,
            'student' => $student,
            'earnedCredits' => $earnedCredits,
            'cGPA' => $cGPA,
            'verificationHash' => $student->getVerificationHash(),
            'verifiedAt' => now()->format('d.m.Y H:i:s'),
        ]);
    }
}
