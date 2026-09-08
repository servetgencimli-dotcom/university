@extends('layouts.portal')

@section('title', 'Rəsmi Akademik Transkript — Khazar University')
@section('header_title', 'Rəsmi Akademik Transkript və Məzuniyyət Auditi')

@section('content')

    <!-- Print Action Bar (Hidden on Print) -->
    <div class="no-print" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 class="font-heading" style="font-size: 1.6rem; color: #fff;">Akademik Transkript & CGPA Göstəricisi</h2>
            <p style="color: #94a3b8; font-size: 0.9rem;">Bütün semestrlər üzrə rəsmi qiymətlər, ECTS kreditləri və 4.0 şkalası üzrə Ümumi Orta Müvəffəqiyyət Göstəricisi (GPA).</p>
        </div>

        <div style="display: flex; gap: 1rem;">
            <button onclick="window.print()" class="btn gradient-btn" style="padding: 0.65rem 1.25rem;">
                <i class="fa-solid fa-print"></i> Transkripti Çap Et / PDF
            </button>
        </div>
    </div>

    @if($hasDebt)
        <div class="no-print" style="margin-bottom: 2rem; background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); border-radius: 0.75rem; padding: 1.25rem; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 1rem; color: #fca5a5;">
                <i class="fa-solid fa-circle-exclamation" style="font-size: 1.5rem;"></i>
                <div>
                    <strong>Rəsmi Transkript Məhdudiyyəti:</strong> Ödənilməmiş təhsil haqqı borcunuz var. Rəsmi möhürlü transkript əldə etmək üçün borcun tam ödənilməsi tələb olunur.
                </div>
            </div>
            <a href="{{ route('portal.finance.index') }}" class="btn btn-primary" style="padding: 0.4rem 0.85rem; font-size: 0.85rem; background: #ef4444; border-color: #ef4444;">
                Borcu Ödə
            </a>
        </div>
    @endif

    <!-- Degree Audit Progress Card -->
    <div class="no-print glass-panel" style="padding: 1.75rem; margin-bottom: 2rem; background: linear-gradient(135deg, rgba(30, 27, 75, 0.8), rgba(15, 23, 42, 0.9)); border-color: rgba(99, 102, 241, 0.3);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
            <div>
                <span class="badge badge-primary" style="font-size: 0.75rem;"><i class="fa-solid fa-graduation-cap"></i> Məzuniyyət və Diplom Auditi</span>
                <h3 class="font-heading" style="font-size: 1.2rem; color: #fff; margin-top: 0.25rem;">Tədris Planının Tamamlanma Dərəcəsi</h3>
            </div>
            <div class="font-heading" style="font-size: 1.8rem; color: #38bdf8;">
                {{ $degreeProgressPercent }}%
            </div>
        </div>

        <div style="background: rgba(255,255,255,0.1); border-radius: 9999px; height: 12px; overflow: hidden; margin-bottom: 0.6rem;">
            <div style="background: linear-gradient(90deg, #38bdf8, #10b981); height: 100%; width: {{ $degreeProgressPercent }}%;"></div>
        </div>

        <div style="display: flex; justify-content: space-between; font-size: 0.85rem; color: #cbd5e1;">
            <span>Qazanılmış Kredit: <strong style="color: #34d399;">{{ $earnedCredits }} ECTS</strong></span>
            <span>Məzuniyyət üçün tələb olunan: <strong>{{ $requiredCredits }} ECTS</strong></span>
            <span>Qalan: <strong>{{ max(0, $requiredCredits - $earnedCredits) }} ECTS</strong></span>
        </div>
    </div>

    <!-- Printable Official Transcript Paper -->
    <div class="transcript-paper" style="background: #ffffff; color: #0f172a; padding: 3rem; border-radius: 0.75rem; box-shadow: 0 10px 30px rgba(0,0,0,0.5); font-family: 'Times New Roman', serif;">
        
        <!-- University Official Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 3px double #002B49; padding-bottom: 1.5rem; margin-bottom: 2rem;">
            <div>
                <div style="font-size: 1.6rem; font-weight: 800; color: #002B49; letter-spacing: 0.02em;">XƏZƏR UNİVERSİTETİ</div>
                <div style="font-size: 1.1rem; font-weight: 600; color: #64748b;">KHAZAR UNIVERSITY</div>
                <div style="font-size: 0.85rem; color: #475569; margin-top: 0.25rem;">Rəsmi Akademik Reyestr və Qeydiyyat Şöbəsi / Office of the Registrar</div>
            </div>

            <div style="text-align: right;">
                <div style="display: inline-block; border: 2px solid #C89D35; border-radius: 0.5rem; padding: 0.4rem 0.8rem; background: #faf5ea; text-align: center;">
                    <div style="font-size: 0.75rem; font-weight: 700; color: #92400e;">RƏSMİ TRANSKRİPT</div>
                    <div style="font-size: 0.7rem; color: #78350f;">OFFICIAL TRANSCRIPT</div>
                </div>
            </div>
        </div>

        <!-- Student Meta Information Grid -->
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; font-size: 0.9rem; margin-bottom: 2rem; background: #f8fafc; padding: 1.25rem; border-radius: 0.5rem; border: 1px solid #e2e8f0;">
            <div>
                <div><strong>Tələbənin Adı / Student Name:</strong> {{ $student->name }}</div>
                <div><strong>Tələbə ID / Student ID:</strong> <span style="font-family: monospace; font-weight: 700;">{{ $student->student_id }}</span></div>
                <div><strong>Fakültə / School:</strong> {{ $student->faculty->name ?? 'Mühəndislik və Təbiət Elmləri' }}</div>
            </div>
            <div>
                <div><strong>Təhsil Pilləsi / Degree Level:</strong> {{ strtoupper($student->degree_level ?? 'Bakalavr') }}</div>
                <div><strong>Qəbul İli / Admission Year:</strong> {{ $student->admission_year }}</div>
                <div><strong>Yekun ÜOMG / Cumulative GPA:</strong> <span style="font-size: 1.1rem; font-weight: 800; color: #002B49;">{{ $student->gpa }} / 4.00</span></div>
            </div>
        </div>

        <!-- Terms Tables -->
        @foreach($termsHistory as $termName => $termEnrollments)
            <div style="margin-bottom: 2rem;">
                <div style="background: #002B49; color: #fff; padding: 0.4rem 0.75rem; font-weight: 700; font-size: 0.95rem; border-radius: 0.25rem 0.25rem 0 0; display: flex; justify-content: space-between;">
                    <span>SEMSETR / TERM: {{ strtoupper($termName) }}</span>
                    <span>Kreditlər: {{ $termEnrollments->sum(fn($e) => $e->courseSection?->course?->credits ?? 0) }} ECTS</span>
                </div>

                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem; border: 1px solid #cbd5e1;">
                    <thead>
                        <tr style="background: #f1f5f9; border-bottom: 1px solid #cbd5e1; color: #334155;">
                            <th style="padding: 0.5rem; border: 1px solid #cbd5e1;">Kod / Code</th>
                            <th style="padding: 0.5rem; border: 1px solid #cbd5e1;">Fənnin Adı / Course Title</th>
                            <th style="padding: 0.5rem; border: 1px solid #cbd5e1; text-align: center;">Kredit</th>
                            <th style="padding: 0.5rem; border: 1px solid #cbd5e1; text-align: center;">Aralıq (50)</th>
                            <th style="padding: 0.5rem; border: 1px solid #cbd5e1; text-align: center;">Final (50)</th>
                            <th style="padding: 0.5rem; border: 1px solid #cbd5e1; text-align: center;">Yekun (100)</th>
                            <th style="padding: 0.5rem; border: 1px solid #cbd5e1; text-align: center;">Hərf (4.0)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($termEnrollments as $enr)
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 0.5rem; border: 1px solid #cbd5e1; font-family: monospace; font-weight: 700;">{{ $enr->courseSection->course->code }}</td>
                                <td style="padding: 0.5rem; border: 1px solid #cbd5e1;">{{ $enr->courseSection->course->name }}</td>
                                <td style="padding: 0.5rem; border: 1px solid #cbd5e1; text-align: center;">{{ $enr->courseSection->course->credits }}</td>
                                <td style="padding: 0.5rem; border: 1px solid #cbd5e1; text-align: center;">{{ $enr->midterm_score }}</td>
                                <td style="padding: 0.5rem; border: 1px solid #cbd5e1; text-align: center;">{{ $enr->final_score }}</td>
                                <td style="padding: 0.5rem; border: 1px solid #cbd5e1; text-align: center; font-weight: 700;">{{ $enr->total_score }}</td>
                                <td style="padding: 0.5rem; border: 1px solid #cbd5e1; text-align: center; font-weight: 700;">
                                    {{ $enr->letter_grade }} ({{ \App\Enums\LetterGrade::tryFrom($enr->letter_grade)?->gradePoint() ?? 0.0 }})
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach

        <!-- Summary & Official Signatures -->
        <div style="margin-top: 3rem; padding-top: 1.5rem; border-top: 2px solid #002B49; display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
            <div style="font-size: 0.8rem; color: #475569; line-height: 1.6;">
                <div><strong>Xəzər Universiteti Qiymətləndirmə Şkalası (4.0 Scale):</strong></div>
                <div>A: 90-100 (4.0) • B: 80-89 (3.0) • C: 70-79 (2.0) • D: 60-69 (1.0) • F: 0-59 (0.0 / Qeyri-kafi)</div>
                <div style="margin-top: 0.5rem;">Bu sənəd Xəzər Universitetinin elektron reyestr sistemi tərəfindən generasiya edilmişdir.</div>
                <div>Tarix / Date: {{ now()->format('d.m.Y') }}</div>
            </div>

            <div style="text-align: center;">
                <div style="font-size: 0.85rem; font-weight: 700; color: #002B49; margin-bottom: 2.5rem;">Rektorluq & Qeydiyyat Departamenti</div>
                <div style="border-top: 1px solid #002B49; padding-top: 0.25rem; font-size: 0.75rem; color: #64748b;">
                    İmza və Rəsmi Möhür Yeri / Seal
                </div>
            </div>
        </div>

    </div>

@endsection

@section('scripts')
<style>
    @media print {
        body { background: #fff !important; }
        .portal-sidebar, .no-print, header { display: none !important; }
        .portal-content { margin-left: 0 !important; padding: 0 !important; width: 100% !important; }
        .transcript-paper { box-shadow: none !important; padding: 0 !important; }
    }
</style>
@endsection
