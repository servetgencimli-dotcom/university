<?php

namespace App\Http\Controllers;

use App\Models\AdmissionsApplication;
use App\Models\ApplicationDocument;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdmissionsController extends Controller
{
    public function applyForm(Request $request)
    {
        $programs = Program::with('department.faculty')->orderBy('name')->get();
        $selectedProgramId = $request->get('program_id');

        return view('pages.admissions.apply', compact('programs', 'selectedProgramId'));
    }

    public function submitApplication(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:50',
            'passport_fin' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string',
            'nationality' => 'nullable|string',
            'high_school_gpa' => 'nullable|numeric|min:0|max:100',
            'exam_score' => 'nullable|integer|min:0|max:700',
            'exam_type' => 'nullable|string',
            'degree_level' => 'required|in:bachelor,master,phd',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,png,doc,docx|max:10240',
        ]);

        $appNumber = 'APP-'.date('Y').'-'.strtoupper(Str::random(6));

        $application = AdmissionsApplication::create([
            'application_number' => $appNumber,
            'user_id' => auth()->id(),
            'program_id' => $validated['program_id'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'passport_fin' => $validated['passport_fin'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'nationality' => $validated['nationality'] ?? 'Azərbaycan',
            'high_school_gpa' => $validated['high_school_gpa'] ?? null,
            'exam_score' => $validated['exam_score'] ?? null,
            'exam_type' => $validated['exam_type'] ?? 'DİM',
            'degree_level' => $validated['degree_level'],
            'status' => 'submitted',
        ]);

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('admissions_docs', 'public');
                ApplicationDocument::create([
                    'admissions_application_id' => $application->id,
                    'document_type' => 'Yüklənmiş Sənəd',
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'file_size_kb' => round($file->getSize() / 1024),
                ]);
            }
        }

        return redirect()->route('admissions.track', ['app_number' => $appNumber])
            ->with('success', "Müraciətiniz qeydə alındı! İzləmə nömrəniz: {$appNumber}");
    }

    public function track(Request $request)
    {
        $appNumber = $request->get('app_number');
        $application = null;

        if ($appNumber) {
            $application = AdmissionsApplication::with(['program.department.faculty', 'documents'])
                ->where('application_number', trim($appNumber))
                ->first();
        }

        return view('pages.admissions.track', compact('application', 'appNumber'));
    }
}
