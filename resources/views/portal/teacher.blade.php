@extends('layouts.portal')

@section('title', 'Müəllim / Professor Kabineti')
@section('header_title', 'Müəllim Portalı & Elektron Jurnal')

@section('content')

    <!-- Teacher Profile Summary Banner -->
    <div class="glass-panel" style="padding: 2rem; margin-bottom: 2.5rem; display: flex; align-items: center; justify-content: space-between;">
        <div style="display: flex; align-items: center; gap: 1.5rem;">
            <div style="width: 65px; height: 65px; background: rgba(16, 185, 129, 0.15); border: 2px solid #34d399; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #34d399;">
                <i class="fa-solid fa-chalkboard-user"></i>
            </div>
            <div>
                <h2 class="font-heading" style="font-size: 1.6rem; color: #fff; margin-bottom: 0.25rem;">{{ $user->name }}</h2>
                <div style="font-size: 0.9rem; color: #38bdf8; font-weight: 600;">{{ $user->role_title }} • {{ $user->faculty->name ?? 'School of Science and Engineering' }}</div>
                <div style="font-size: 0.8rem; color: #94a3b8; margin-top: 0.35rem;">Departament: {{ $user->department->name ?? 'Computer Science' }} • İşçi ID: <span class="font-mono">{{ $user->staff_id }}</span></div>
            </div>
        </div>

        <div style="text-align: right;">
            <div style="font-size: 0.8rem; color: #94a3b8;">Tədris Edilən Qruplar</div>
            <div class="font-heading" style="font-size: 1.8rem; color: #fff;">{{ $taughtSections->count() }} Qrup</div>
        </div>
    </div>

    <!-- Section: Grade Book & Student Journal -->
    <div id="grading-section" class="glass-panel" style="padding: 2rem; margin-bottom: 2.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div>
                <h3 class="font-heading" style="font-size: 1.35rem; color: #fff;">
                    <i class="fa-solid fa-clipboard-check text-emerald-400"></i> Elektron Jurnal və Qiymətləndirmə (4.0 Şkalası)
                </h3>
                <div style="font-size: 0.85rem; color: #94a3b8;">Aralıq (maks. 50) və yekun (maks. 50) ballarını daxil edin. Xəzər Universiteti A/B/C/D/F hərfi və tələbənin GPA-sı avtomatik hesablanır.</div>
            </div>
        </div>

        @foreach($taughtSections as $sec)
            <div class="glass-card" style="padding: 1.75rem; margin-bottom: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1.25rem; border-bottom: 1px solid rgba(255,255,255,0.06); padding-bottom: 0.75rem;">
                    <div>
                        <span class="font-mono badge badge-primary">{{ $sec->course->code }}</span>
                        <span class="font-heading" style="font-size: 1.2rem; color: #fff; margin-left: 0.5rem;">{{ $sec->course->name }}</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <span style="font-size: 0.85rem; color: #94a3b8;">
                            <i class="fa-solid fa-clock"></i> {{ $sec->day_of_week }}, {{ $sec->start_time }} - {{ $sec->end_time }} • {{ $sec->room_number }}
                        </span>
                        <a href="{{ route('portal.grading.show', $sec->id) }}" class="btn btn-primary" style="font-size: 0.8rem; padding: 0.4rem 0.95rem; background: linear-gradient(135deg, #00A3E0, #0284c7);">
                            <i class="fa-solid fa-sliders"></i> Bal Bölgüsü & Elektron Jurnal
                        </a>
                    </div>
                </div>

                @if($sec->enrollments->isEmpty())
                    <div style="color: #94a3b8; font-size: 0.85rem;">Bu qrupda hələ qeydiyyatdan keçmiş tələbə yoxdur.</div>
                @else
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem;">
                            <thead>
                                <tr style="color: #94a3b8; border-bottom: 1px solid rgba(255,255,255,0.08);">
                                    <th style="padding: 0.6rem;">TƏLƏBƏ</th>
                                    <th style="padding: 0.6rem;">TƏLƏBƏ ID</th>
                                    <th style="padding: 0.6rem;">ARALIQ (50)</th>
                                    <th style="padding: 0.6rem;">FINAL (50)</th>
                                    <th style="padding: 0.6rem;">DAVAMİYYƏT %</th>
                                    <th style="padding: 0.6rem;">YEKUN</th>
                                    <th style="padding: 0.6rem;">HƏRFİ (4.0)</th>
                                    <th style="padding: 0.6rem;">ƏMƏLİYYAT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sec->enrollments as $enr)
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                                        <td style="padding: 0.75rem 0.6rem; font-weight: 600; color: #fff;">{{ $enr->student->name }}</td>
                                        <td class="font-mono" style="padding: 0.6rem; color: #38bdf8;">{{ $enr->student->student_id }}</td>
                                        <td style="padding: 0.6rem;">
                                            <input form="grade-form-{{ $enr->id }}" type="number" step="0.5" min="0" max="50" name="midterm_score" value="{{ $enr->midterm_score }}" class="form-input" style="width: 80px; padding: 0.3rem 0.5rem; font-size: 0.85rem;" required>
                                        </td>
                                        <td style="padding: 0.6rem;">
                                            <input form="grade-form-{{ $enr->id }}" type="number" step="0.5" min="0" max="50" name="final_score" value="{{ $enr->final_score }}" class="form-input" style="width: 80px; padding: 0.3rem 0.5rem; font-size: 0.85rem;" required>
                                        </td>
                                        <td style="padding: 0.6rem;">
                                            <input form="grade-form-{{ $enr->id }}" type="number" step="1" min="0" max="100" name="attendance_percentage" value="{{ $enr->attendance_percentage }}" class="form-input" style="width: 75px; padding: 0.3rem 0.5rem; font-size: 0.85rem;" required>
                                        </td>
                                        <td style="padding: 0.6rem; font-weight: 700; color: #38bdf8; font-size: 1rem;">
                                            {{ $enr->total_score }}
                                        </td>
                                        <td style="padding: 0.6rem;">
                                            <span class="badge {{ $enr->letter_grade === 'A' ? 'badge-success' : ($enr->letter_grade === 'F' ? 'badge-danger' : 'badge-primary') }}">
                                                {{ $enr->letter_grade }} ({{ \App\Enums\LetterGrade::tryFrom($enr->letter_grade)?->gradePoint() ?? 0.0 }})
                                            </span>
                                        </td>
                                        <td style="padding: 0.6rem;">
                                            <form id="grade-form-{{ $enr->id }}" action="{{ route('portal.grades.update', $enr->id) }}" method="POST" style="margin: 0; display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-primary" style="padding: 0.35rem 0.75rem; font-size: 0.75rem;">Yadda saxla</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Advised Theses -->
    @if($advisedTheses->isNotEmpty())
        <div id="theses-section" class="glass-panel" style="padding: 2rem;">
            <h3 class="font-heading" style="font-size: 1.35rem; color: #fff; margin-bottom: 1.25rem;">
                <i class="fa-solid fa-microscope text-purple-400"></i> Rəhbərlik Etdiyiniz Magistr Dissertasiyaları
            </h3>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @foreach($advisedTheses as $th)
                    <div class="glass-card" style="padding: 1.25rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                            <span class="badge badge-master">Magistr Dissertasiyası</span>
                            <span style="font-size: 0.85rem; color: #34d399; font-weight: 600;">İcra: {{ $th->progress_percentage }}%</span>
                        </div>
                        <h4 class="font-heading" style="font-size: 1.15rem; color: #fff; margin-bottom: 0.25rem;">{{ $th->title }}</h4>
                        <div style="font-size: 0.85rem; color: #94a3b8;">Magistrant: <strong style="color: #cbd5e1;">{{ $th->student->name }}</strong> (Müdafiə: {{ $th->expected_defense_date?->format('d.m.Y') }})</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

@endsection
