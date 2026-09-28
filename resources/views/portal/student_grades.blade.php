@extends('layouts.portal')

@section('title', 'Semestr Qiymətləri — Xəzər Universiteti')
@section('header_title', 'Semestr Qiymətləri və Bal Bölgüsü')

@section('content')

    <!-- Top Banner & Semester Info -->
    <div class="glass-panel" style="padding: 2rem; margin-bottom: 2rem; background: linear-gradient(135deg, rgba(6, 17, 36, 0.9), rgba(12, 24, 44, 0.95));">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
            <div>
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                    <span class="badge badge-student"><i class="fa-solid fa-graduation-cap"></i> Bakalavr Tələbəsi</span>
                    <span class="badge badge-primary"><i class="fa-solid fa-calendar-check"></i> {{ $termCode }}</span>
                </div>
                <h2 class="font-heading" style="font-size: 1.8rem; color: #fff; margin-bottom: 0.25rem;">Semestr Qiymət Cədvəli</h2>
                <p style="color: #94a3b8; font-size: 0.9rem;">
                    Müəllimlərinizin təyin etdiyi fərdi bal bölgüsü (seminar, kollokvium, sərbəst iş, davamiyyət, final) və toplanan cari ballarınız.
                </p>
            </div>

            <!-- Quick Action Dropdown Menu & Appeals Link -->
            <div style="display: flex; gap: 0.75rem; align-items: center;">
                <a href="{{ route('portal.appeals.index', ['type' => 'grade_objection']) }}" class="btn btn-outline" style="font-size: 0.85rem; border-color: rgba(245, 158, 11, 0.4); color: #fbbf24;">
                    <i class="fa-solid fa-scale-balanced"></i> İmtahan Nəticəsinə Etiraz Et
                </a>
                <a href="{{ route('portal.transcript') }}" class="btn btn-primary" style="font-size: 0.85rem;">
                    <i class="fa-solid fa-file-invoice"></i> Tam Rəsmi Transkript
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
        <div class="glass-panel" style="padding: 1.25rem;">
            <div style="font-size: 0.8rem; color: #94a3b8; margin-bottom: 0.35rem;">Cari Semestr Fənləri</div>
            <div class="font-heading" style="font-size: 1.8rem; color: #fff;">{{ $enrollments->count() }} Fənn</div>
            <div style="font-size: 0.75rem; color: #38bdf8; margin-top: 0.25rem;">Cəm {{ $enrollments->sum(fn($e) => $e->courseSection?->course?->credits ?? 0) }} ECTS Kredit</div>
        </div>

        <div class="glass-panel" style="padding: 1.25rem;">
            <div style="font-size: 0.8rem; color: #94a3b8; margin-bottom: 0.35rem;">Ümumi Orta Bal</div>
            <div class="font-heading" style="font-size: 1.8rem; color: #34d399;">
                {{ $enrollments->count() > 0 ? number_format($enrollments->avg('total_score'), 1) : '0' }} <span style="font-size: 1rem; color: #94a3b8;">/ 100</span>
            </div>
            <div style="font-size: 0.75rem; color: #34d399; margin-top: 0.25rem;">Uğurlu Tədris Göstəricisi</div>
        </div>

        <div class="glass-panel" style="padding: 1.25rem;">
            <div style="font-size: 0.8rem; color: #94a3b8; margin-bottom: 0.35rem;">Ümumi GPA (ÜOMG)</div>
            <div class="font-heading" style="font-size: 1.8rem; color: #818cf8;">{{ $user->gpa }} <span style="font-size: 1rem; color: #94a3b8;">/ 4.0</span></div>
            <div style="font-size: 0.75rem; color: #818cf8; margin-top: 0.25rem;">4.0 Şkalası ilə</div>
        </div>

        <div class="glass-panel" style="padding: 1.25rem;">
            <div style="font-size: 0.8rem; color: #94a3b8; margin-bottom: 0.35rem;">Qazanılmış Kreditlər</div>
            <div class="font-heading" style="font-size: 1.8rem; color: #fbbf24;">{{ $user->earned_credits }} ECTS</div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">Məzuniyyət üçün: 240 ECTS</div>
        </div>
    </div>

    <!-- Courses & Detailed Grade Breakdown Cards -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem; margin-bottom: 2.5rem;">
        @forelse($enrollments as $enr)
            @php
                $course = $enr->courseSection?->course;
                $section = $enr->courseSection;
                $components = $section?->gradeComponents ?? collect();
                $gradeItems = $enr->gradeItems->keyBy('grade_component_id');
                $totalScore = (float)$enr->total_score;
                $isPass = $totalScore >= 51;
            @endphp
            <div class="glass-panel" style="padding: 1.75rem; border-left: 4px solid {{ $isPass ? '#10b981' : '#f59e0b' }};">
                
                <!-- Course Header Info -->
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.06); padding-bottom: 1rem;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.35rem;">
                            <span class="font-mono badge badge-primary">{{ $course?->code }}</span>
                            <span style="font-size: 0.85rem; color: #38bdf8;">{{ $course?->credits }} ECTS Kredit</span>
                            <span style="font-size: 0.8rem; color: #94a3b8;">• Otaq: {{ $section?->room_number }}</span>
                        </div>
                        <h3 class="font-heading" style="font-size: 1.35rem; color: #fff;">{{ $course?->name }}</h3>
                        <div style="font-size: 0.85rem; color: #cbd5e1; margin-top: 0.25rem;">
                            <i class="fa-solid fa-chalkboard-user text-emerald-400"></i> Müəllim: <strong>{{ $section?->teacher?->name ?? 'Təyin olunmayıb' }}</strong>
                            <span style="color: #64748b; margin: 0 0.5rem;">|</span>
                            <i class="fa-regular fa-clock text-sky-400"></i> {{ $section?->day_of_week }}, {{ $section?->start_time }} - {{ $section?->end_time }}
                        </div>
                    </div>

                    <!-- Total Score & Letter Grade -->
                    <div style="text-align: right; display: flex; align-items: center; gap: 1.25rem;">
                        <div>
                            <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase;">Yekun Bal</div>
                            <div class="font-heading" style="font-size: 2.2rem; color: {{ $isPass ? '#34d399' : '#f87171' }}; line-height: 1;">
                                {{ number_format($totalScore, 1) }}
                            </div>
                        </div>
                        <div style="padding-left: 1.25rem; border-left: 1px solid rgba(255,255,255,0.1); text-align: center;">
                            <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase;">Hərfi Qiymət</div>
                            <div style="margin-top: 0.2rem;">
                                <span class="badge {{ $enr->letter_grade === 'A' ? 'badge-success' : ($enr->letter_grade === 'F' ? 'badge-danger' : 'badge-primary') }}" style="font-size: 1.1rem; padding: 0.35rem 0.85rem;">
                                    {{ $enr->letter_grade }}
                                </span>
                            </div>
                            <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.2rem;">
                                {{ \App\Enums\LetterGrade::tryFrom($enr->letter_grade)?->gradePoint() ?? '0.0' }} / 4.0
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Teacher Grade Breakdown (Bal Bölgüsü) Details -->
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem;">
                        <span style="font-size: 0.85rem; font-weight: 700; color: #38bdf8; text-transform: uppercase; letter-spacing: 0.05em;">
                            <i class="fa-solid fa-list-check"></i> Müəllimin Bal Bölgüsü və Fərdi Nəticələriniz
                        </span>
                        <a href="{{ route('portal.appeals.index', ['type' => 'grade_objection', 'course_section_id' => $section?->id]) }}" class="text-xs text-amber-400 hover:text-amber-300 transition" style="text-decoration: underline;">
                            <i class="fa-solid fa-circle-exclamation"></i> Bu fənnin balına etiraz et (Apellyasiya)
                        </a>
                    </div>

                    @if($components->isEmpty())
                        <!-- Fallback classic midterm/final if teacher hasn't configured custom breakdown yet -->
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-top: 0.75rem;">
                            <div class="glass-card" style="padding: 1rem;">
                                <div style="font-size: 0.75rem; color: #94a3b8;">Aralıq Qiymətləndirmə (Maks. 50)</div>
                                <div class="font-heading" style="font-size: 1.4rem; color: #fff; margin-top: 0.25rem;">
                                    {{ $enr->midterm_score }} <span style="font-size: 0.85rem; color: #94a3b8;">/ 50</span>
                                </div>
                            </div>
                            <div class="glass-card" style="padding: 1rem;">
                                <div style="font-size: 0.75rem; color: #94a3b8;">Final İmtahanı (Maks. 50)</div>
                                <div class="font-heading" style="font-size: 1.4rem; color: #fff; margin-top: 0.25rem;">
                                    {{ $enr->final_score }} <span style="font-size: 0.85rem; color: #94a3b8;">/ 50</span>
                                </div>
                            </div>
                            <div class="glass-card" style="padding: 1rem;">
                                <div style="font-size: 0.75rem; color: #94a3b8;">Davamiyyət Göstəricisi</div>
                                <div class="font-heading" style="font-size: 1.4rem; color: #38bdf8; margin-top: 0.25rem;">
                                    {{ $enr->attendance_percentage }}%
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Detailed Custom Grade Components Grid -->
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.85rem;">
                            @foreach($components as $comp)
                                @php
                                    $item = $gradeItems->get($comp->id);
                                    $score = $item ? (float)$item->score : 0.0;
                                    $max = (float)$comp->max_score;
                                    $pct = $max > 0 ? min(100, round(($score / $max) * 100)) : 0;
                                @endphp
                                <div class="glass-card" style="padding: 1rem; position: relative; overflow: hidden;">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                        <div style="font-size: 0.82rem; font-weight: 600; color: #e2e8f0; line-height: 1.2;">
                                            {{ $comp->name }}
                                        </div>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.5rem;">
                                        <div class="font-heading" style="font-size: 1.35rem; color: {{ $score >= ($max * 0.5) ? '#34d399' : '#f87171' }};">
                                            {{ $score }}
                                        </div>
                                        <div style="font-size: 0.75rem; color: #94a3b8;">
                                            maks: <strong>{{ $max }}</strong> bal
                                        </div>
                                    </div>
                                    <!-- Progress Bar -->
                                    <div style="width: 100%; height: 5px; background: rgba(255,255,255,0.08); border-radius: 999px; overflow: hidden;">
                                        <div style="width: {{ $pct }}%; height: 100%; background: linear-gradient(90deg, #00A3E0, #34d399); border-radius: 999px;"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        @empty
            <div class="glass-panel" style="padding: 3rem; text-align: center; color: #94a3b8;">
                <i class="fa-solid fa-book-open text-4xl mb-3 text-slate-500"></i>
                <div style="font-size: 1.1rem; color: #fff; font-weight: 600;">Bu semestr üçün qeydiyyatdan keçmiş fənn tapılmadı.</div>
                <div style="font-size: 0.85rem; margin-top: 0.5rem;">Fənn seçimi etmək üçün Fənn Seçimi (Registration) bölməsinə keçin.</div>
                <a href="{{ route('portal.registration.index') }}" class="btn btn-primary" style="margin-top: 1.25rem;">
                    <i class="fa-solid fa-plus"></i> Fənn Seçiminə Get
                </a>
            </div>
        @endforelse
    </div>

@endsection
