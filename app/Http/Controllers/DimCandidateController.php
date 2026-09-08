<?php

namespace App\Http\Controllers;

use App\Models\AdmissionsApplication;
use App\Models\Program;
use App\Models\Role;
use App\Models\TuitionInvoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DimCandidateController extends Controller
{
    /**
     * List all DIM candidates.
     */
    public function index(Request $request)
    {
        $query = AdmissionsApplication::with(['program.department.faculty', 'user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('dim_work_number', 'like', "%{$search}%")
                    ->orWhere('passport_fin', 'like', "%{$search}%")
                    ->orWhere('application_number', 'like', "%{$search}%");
            });
        }

        $candidates = $query->latest()->paginate(25)->withQueryString();
        $programs = Program::with('department.faculty')->get();

        return view('portal.dim_candidates', compact('candidates', 'programs'));
    }

    /**
     * Add new DIM candidate to registry.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email',
            'phone' => 'required|string|max:50',
            'passport_fin' => 'required|string|max:20',
            'program_id' => 'required|exists:programs,id',
            'dim_work_number' => 'required|string|max:50',
            'exam_score' => 'required|numeric|min:0|max:700',
            'secim_sira' => 'nullable|integer|min:1',
            'odenis_formasi' => 'required|in:Dövlət Sifarişli,Ödənişli Əsaslarla',
            'degree_level' => 'required|in:bachelor,master,phd',
        ]);

        $appNumber = 'KU-DIM-'.date('Y').'-'.strtoupper(Str::random(4));

        $candidate = AdmissionsApplication::create([
            'application_number' => $appNumber,
            'dim_work_number' => $validated['dim_work_number'],
            'program_id' => $validated['program_id'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'passport_fin' => strtoupper($validated['passport_fin']),
            'exam_score' => $validated['exam_score'],
            'exam_type' => 'DİM',
            'secim_sira' => $validated['secim_sira'] ?? 1,
            'odenis_formasi' => $validated['odenis_formasi'],
            'degree_level' => $validated['degree_level'],
            'status' => 'under_review',
        ]);

        return back()->with('success', "DİM namizədi ({$candidate->full_name}, İş №: {$candidate->dim_work_number}) reyestrə daxil edildi.");
    }

    /**
     * Accept DIM candidate and generate official University Student account.
     */
    public function acceptAsStudent(Request $request, $id)
    {
        $candidate = AdmissionsApplication::with('program.department.faculty')->findOrFail($id);

        if ($candidate->status === 'accepted' && $candidate->user_id) {
            return back()->with('error', 'Bu namizəd artıq qəbul edilib və tələbə hesabı mövcuddur.');
        }

        $program = $candidate->program;
        $faculty = $program?->department?->faculty;
        $department = $program?->department;

        // Generate student ID
        $year = date('Y');
        $studentId = 'KU-'.$year.'-'.rand(1000, 9999);
        while (User::where('student_id', $studentId)->exists()) {
            $studentId = 'KU-'.$year.'-'.rand(1000, 9999);
        }

        // Generate official university email
        $cleanFirst = Str::slug($candidate->first_name);
        $cleanLast = Str::slug($candidate->last_name);
        $officialEmail = "{$cleanFirst}.{$cleanLast}@khazar.org";
        $counter = 1;
        while (User::where('email', $officialEmail)->exists()) {
            $officialEmail = "{$cleanFirst}.{$cleanLast}{$counter}@khazar.org";
            $counter++;
        }

        $defaultPassword = 'password';

        $roleName = $candidate->degree_level === 'master' ? 'master_student' : 'student';
        $roleTitle = $candidate->degree_level === 'master'
            ? 'Magistrant & Tədqiqatçı (I Kurs)'
            : 'Bakalavr Tələbəsi (I Kurs)';

        $student = new User([
            'name' => $candidate->full_name,
            'email' => $officialEmail,
            'password' => Hash::make($defaultPassword),
            'role_title' => $roleTitle,
            'student_id' => $studentId,
            'faculty_id' => $faculty?->id,
            'department_id' => $department?->id,
            'degree_level' => $candidate->degree_level,
            'admission_year' => (int) $year,
            'phone' => $candidate->phone,
            'bio' => "DİM imtahan nəticələrinə əsasən ({$candidate->exam_score} bal, {$candidate->odenis_formasi}) {$program->name} ixtisasına qəbul olunmuşdur.",
            'status' => 'active',
        ]);

        $student->role = $roleName;
        $student->save();

        $roleModel = Role::where('name', $roleName)->first();
        if ($roleModel) {
            $student->roles()->sync([$roleModel->id]);
        }

        // Update application
        $candidate->update([
            'status' => 'accepted',
            'user_id' => $student->id,
            'reviewer_notes' => 'DİM nəticələri əsasında universitetə rəsmi qəbul edildi. Tələbə kabineti aktivləşdirildi.',
        ]);

        // Create tuition invoice if paid
        if ($candidate->odenis_formasi === 'Ödənişli Əsaslarla') {
            TuitionInvoice::create([
                'student_id' => $student->id,
                'invoice_number' => 'KU-INV-'.$year.'-'.rand(1000, 9999),
                'semester_term' => "{$year}-Payız",
                'amount' => ($program->tuition_fee_azn ?? 4000.00) / 2,
                'due_date' => now()->addDays(30),
                'status' => 'pending',
                'payment_method' => 'Bank / Onlayn Ödəniş',
            ]);
        }

        return back()->with('success', "Namizəd qəbul edildi! Rəsmi Tələbə Hesabı yaradıldı: E-poçt: {$officialEmail}, Tələbə ID: {$studentId}, Şifrə: {$defaultPassword}");
    }

    /**
     * Reject candidate.
     */
    public function reject(Request $request, $id)
    {
        $candidate = AdmissionsApplication::findOrFail($id);
        $candidate->update([
            'status' => 'rejected',
            'reviewer_notes' => $request->input('notes', 'Qəbul komissiyası tərəfindən imtina edildi.'),
        ]);

        return back()->with('success', "Namizədin (#{$candidate->application_number}) müraciətinə imtina verildi.");
    }
}
