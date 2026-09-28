@extends('layouts.portal')

@section('title', 'Self-Service Fənn Seçimi (Registration) — Khazar University')
@section('header_title', 'Elektron Fənn Qeydiyyatı & Semestr Tədris Planı')

@section('content')

    <!-- Registration Header & Status -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-bottom: 2.5rem; align-items: stretch;">
        
        <div class="glass-panel" style="padding: 2rem; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <div>
                        <span class="badge badge-primary" style="margin-bottom: 0.35rem;">{{ $currentTerm->name ?? '2026 Bahar Semestri' }}</span>
                        <h2 class="font-heading" style="font-size: 1.6rem; color: #fff;">Fənn Seçimi & Qeydiyyatı</h2>
                    </div>

                    <div>
                        @if($currentTerm && $currentTerm->registration_open)
                            <span class="badge badge-success" style="font-size: 0.85rem; padding: 0.4rem 0.85rem;">
                                <i class="fa-solid fa-lock-open"></i> Qeydiyyat Açıqdır
                            </span>
                        @else
                            <span class="badge badge-danger" style="font-size: 0.85rem; padding: 0.4rem 0.85rem;">
                                <i class="fa-solid fa-lock"></i> Qeydiyyat Bağlıdır
                            </span>
                        @endif
                    </div>
                </div>

                <p style="color: #cbd5e1; font-size: 0.9rem; line-height: 1.6;">
                    Semestr üzrə tədris planınıza uyğun fənləri seçin. Kredit limiti maksimum <strong>{{ $currentTerm->max_credits_limit ?? 30 }} ECTS</strong> kreditdir. Prerekviziti olan fənləri yalnız əvvəlki fənni uğurla bitirdikdə seçə bilərsiniz.
                </p>
            </div>

            <!-- Debt Warning if any -->
            @if($user->hasUnpaidDebt())
                <div style="margin-top: 1rem; background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); border-radius: 0.75rem; padding: 1rem; display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; color: #fca5a5; font-size: 0.88rem;">
                        <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.3rem;"></i>
                        <div>
                            <strong>Ödənilməmiş Borc Bloklaması:</strong> Cari semestr üzrə {{ $user->totalDebtAmount() }} ₼ ödənilməmiş təhsil haqqı borcunuz var.
                        </div>
                    </div>
                    <a href="{{ route('portal.finance.index') }}" class="btn btn-primary" style="padding: 0.4rem 0.85rem; font-size: 0.8rem; background: #ef4444; border-color: #ef4444;">
                        Fakturanı Ödə
                    </a>
                </div>
            @endif
        </div>

        <!-- Credit Meter & Progress -->
        <div class="glass-panel" style="padding: 2rem; background: linear-gradient(135deg, rgba(30, 27, 75, 0.8), rgba(15, 23, 42, 0.9)); border-color: rgba(99, 102, 241, 0.3); display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <div style="font-size: 0.8rem; color: #94a3b8; text-transform: uppercase; font-weight: 700;">Seçilmiş Semestr Krediti</div>
                    @if($user->gpa >= 3.50)
                        <span class="badge" style="background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4); font-size: 0.7rem;">
                            <i class="fa-solid fa-star"></i> Əlaçı Limiti (36 ECTS)
                        </span>
                    @elseif($user->gpa > 0 && $user->gpa < 2.00)
                        <span class="badge" style="background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); font-size: 0.7rem;">
                            <i class="fa-solid fa-triangle-exclamation"></i> Kəsir Limiti (24 ECTS)
                        </span>
                    @endif
                </div>

                <div style="display: flex; align-items: baseline; gap: 0.5rem; margin-bottom: 1rem;">
                    <span class="font-heading" style="font-size: 2.5rem; color: #38bdf8;">{{ $totalSelectedCredits }}</span>
                    <span style="color: #94a3b8; font-size: 1.1rem;">/ {{ $maxCreditLimit }} ECTS</span>
                </div>

                <!-- Progress Bar -->
                <div style="background: rgba(255,255,255,0.1); border-radius: 9999px; height: 12px; overflow: hidden; margin-bottom: 0.75rem;">
                    <div style="background: linear-gradient(90deg, #38bdf8, #818cf8); height: 100%; width: {{ min(100, ($totalSelectedCredits / $maxCreditLimit) * 100) }}%;"></div>
                </div>

                <div style="font-size: 0.8rem; color: #cbd5e1; display: flex; justify-content: space-between;">
                    <span>Qalan limit:</span>
                    <strong>{{ max(0, $maxCreditLimit - $totalSelectedCredits) }} ECTS</strong>
                </div>
            </div>

            <div style="padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.08); font-size: 0.8rem; color: #94a3b8;">
                Qeydiyyatdan keçmiş fənlər: <strong style="color: #fff;">{{ $enrolledSections->count() }} fənn</strong>
            </div>
        </div>

    </div>

    <!-- Section 1: Enrolled Courses (Selected by Student) -->
    <div class="glass-panel" style="padding: 2rem; margin-bottom: 2.5rem;">
        <h3 class="font-heading" style="font-size: 1.3rem; color: #fff; margin-bottom: 1.25rem;">
            <i class="fa-solid fa-clipboard-check text-sky-400"></i> Cari Semestrdə Qeydiyyatdan Keçdiyiniz Fənlər
        </h3>

        @if($enrolledSections->isEmpty())
            <div style="padding: 2rem; text-align: center; color: #94a3b8; background: rgba(255,255,255,0.02); border-radius: 0.75rem;">
                Hələ heç bir fənnə qeydiyyatdan keçməmisiniz. Aşağıdakı siyahıdan fənləri seçin.
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.1); color: #94a3b8; font-size: 0.8rem; text-transform: uppercase;">
                            <th style="padding: 0.75rem 1rem;">FƏNN KODU</th>
                            <th style="padding: 0.75rem 1rem;">FƏNN ADI</th>
                            <th style="padding: 0.75rem 1rem;">MÜƏLLİM</th>
                            <th style="padding: 0.75rem 1rem;">KREDİT</th>
                            <th style="padding: 0.75rem 1rem;">CƏDVƏL & OTAQ</th>
                            <th style="padding: 0.75rem 1rem;">LMS OTAĞI</th>
                            <th style="padding: 0.75rem 1rem; text-align: right;">ƏMƏLİYYAT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($enrolledSections as $enr)
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                <td class="font-mono" style="padding: 1rem; color: #818cf8; font-weight: 700;">{{ $enr->courseSection->course->code }}</td>
                                <td style="padding: 1rem; font-weight: 600; color: #fff;">{{ $enr->courseSection->course->name }}</td>
                                <td style="padding: 1rem; color: #cbd5e1;">{{ $enr->courseSection->teacher->name ?? 'Professor' }}</td>
                                <td style="padding: 1rem; font-weight: 700; color: #38bdf8;">{{ $enr->courseSection->course->credits }} ECTS</td>
                                <td style="padding: 1rem; font-size: 0.85rem; color: #cbd5e1;">
                                    <div>{{ $enr->courseSection->day_of_week }} ({{ $enr->courseSection->start_time }}-{{ $enr->courseSection->end_time }})</div>
                                    <div style="color: #94a3b8; font-size: 0.75rem;">{{ $enr->courseSection->room_number }}</div>
                                </td>
                                <td style="padding: 1rem;">
                                    <a href="{{ route('portal.lms.course', $enr->course_section_id) }}" class="btn btn-outline" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; color: #38bdf8;">
                                        <i class="fa-solid fa-chalkboard"></i> LMS Otağı
                                    </a>
                                </td>
                                <td style="padding: 1rem; text-align: right;">
                                    <form action="{{ route('portal.registration.drop', $enr->id) }}" method="POST" onsubmit="return confirm('Bu fənnin qeydiyyatını ləğv etmək istəyirsiniz?');" style="margin: 0;">
                                        @csrf
                                        <button type="submit" class="btn btn-outline" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; color: #f87171; border-color: rgba(239, 68, 68, 0.3);">
                                            <i class="fa-solid fa-trash-can"></i> İmtina (Drop)
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Section 2: Available Courses to Enroll -->
    <div class="glass-panel" style="padding: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div>
                <h3 class="font-heading" style="font-size: 1.3rem; color: #fff;">
                    <i class="fa-solid fa-layer-group text-emerald-400"></i> Qeydiyyat üçün Açıq Olan Fənlər və Qruplar
                </h3>
                <p style="font-size: 0.85rem; color: #94a3b8;">İxtisasınız və semestriniz üçün təklif olunan akademik fənlər (Prerekvizit & Saat toqquşması analizi aktivdir)</p>
            </div>
            <span class="badge badge-student">{{ $availableSections->count() }} Qrup Açıqdır</span>
        </div>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @foreach($availableSections as $sec)
                @php
                    $isAlreadyEnrolled = $enrolledSections->contains('course_section_id', $sec->id);
                    $availableSeats = $sec->availableSeatsCount();
                    $isFull = $sec->isFull();
                    $analysis = $sectionsAnalysis[$sec->id] ?? ['prereq_status' => 'passed', 'prereq_name' => null, 'schedule_conflict' => null];
                    $prereqFailed = $analysis['prereq_status'] === 'failed';
                    $hasConflict = !empty($analysis['schedule_conflict']);
                @endphp

                <div class="glass-card" style="padding: 1.5rem; display: grid; grid-template-columns: 2.5fr 1.5fr 1fr auto; gap: 1.5rem; align-items: center; border-left: 4px solid {{ $isAlreadyEnrolled ? '#10b981' : ($isFull || $prereqFailed || $hasConflict ? '#ef4444' : '#00A3E0') }};">
                    
                    <!-- Course Info -->
                    <div>
                        <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.6rem; margin-bottom: 0.35rem;">
                            <span class="font-mono" style="color: #818cf8; font-weight: 700; font-size: 0.85rem;">{{ $sec->course->code }}</span>
                            <span class="badge {{ $sec->course->type === 'compulsory' ? 'badge-primary' : 'badge-warning' }}" style="font-size: 0.7rem;">
                                {{ $sec->course->type === 'compulsory' ? 'Məcburi' : 'Seçmə' }}
                            </span>

                            @if($sec->course->prerequisite)
                                @if($prereqFailed)
                                    <span style="font-size: 0.75rem; color: #f87171; background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); padding: 0.15rem 0.5rem; border-radius: 4px; font-weight: 600;">
                                        <i class="fa-solid fa-circle-xmark"></i> Prerekvizit Çatışmır: {{ $analysis['prereq_name'] }}
                                    </span>
                                @else
                                    <span style="font-size: 0.75rem; color: #34d399; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); padding: 0.15rem 0.5rem; border-radius: 4px;">
                                        <i class="fa-solid fa-circle-check"></i> Prerekvizit Ödənilib: {{ $sec->course->prerequisite->code }}
                                    </span>
                                @endif
                            @endif

                            @if($hasConflict)
                                <span style="font-size: 0.75rem; color: #fbbf24; background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); padding: 0.15rem 0.5rem; border-radius: 4px; font-weight: 600;">
                                    <i class="fa-solid fa-triangle-exclamation"></i> Saat Toqquşması: {{ $analysis['schedule_conflict'] }}
                                </span>
                            @endif
                        </div>

                        <h4 class="font-heading" style="font-size: 1.15rem; color: #fff; margin-bottom: 0.35rem;">{{ $sec->course->name }}</h4>
                        <div style="font-size: 0.85rem; color: #94a3b8;">
                            Müəllim: <strong style="color: #cbd5e1;">{{ $sec->teacher->name ?? 'Professor' }}</strong>
                        </div>
                    </div>

                    <!-- Schedule & Room -->
                    <div style="font-size: 0.85rem;">
                        <div style="color: #fff; font-weight: 600; margin-bottom: 0.2rem;">
                            <i class="fa-regular fa-clock text-sky-400"></i> {{ $sec->day_of_week }}
                        </div>
                        <div style="color: #38bdf8; font-family: monospace;">{{ $sec->start_time }} - {{ $sec->end_time }}</div>
                        <div style="color: #94a3b8; font-size: 0.75rem; margin-top: 0.2rem;">{{ $sec->room_number }}</div>
                    </div>

                    <!-- Credits & Capacity -->
                    <div>
                        <div style="font-size: 1.1rem; font-weight: 700; color: #38bdf8; margin-bottom: 0.2rem;">
                            {{ $sec->course->credits }} ECTS
                        </div>
                        <div style="font-size: 0.75rem; color: {{ $isFull ? '#f87171' : '#34d399' }};">
                            @if($isFull)
                                <i class="fa-solid fa-circle-xmark"></i> Yer yoxdur (0/{{ $sec->capacity }})
                            @else
                                <i class="fa-solid fa-circle-check"></i> {{ $availableSeats }} boş yer qalıb
                            @endif
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div>
                        @if($isAlreadyEnrolled)
                            <button class="btn btn-outline" disabled style="padding: 0.5rem 1rem; font-size: 0.85rem; color: #34d399; border-color: rgba(16, 185, 129, 0.4);">
                                <i class="fa-solid fa-check"></i> Seçilib
                            </button>
                        @elseif($prereqFailed)
                            <button class="btn btn-outline" disabled title="Prerekvizit fənn keçilməyib" style="padding: 0.5rem 1rem; font-size: 0.85rem; color: #f87171; border-color: rgba(239, 68, 68, 0.3);">
                                <i class="fa-solid fa-ban"></i> Prerekvizit Qadağası
                            </button>
                        @elseif($hasConflict)
                            <button class="btn btn-outline" disabled title="Dərs saatı toqquşur" style="padding: 0.5rem 1rem; font-size: 0.85rem; color: #fbbf24; border-color: rgba(245, 158, 11, 0.3);">
                                <i class="fa-solid fa-clock-rotate-left"></i> Saat Toqquşur
                            </button>
                        @elseif($isFull)
                            <button class="btn btn-outline" disabled style="padding: 0.5rem 1rem; font-size: 0.85rem; color: #64748b;">
                                Tutum Dolub
                            </button>
                        @else
                            <form action="{{ route('portal.registration.enroll', $sec->id) }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" class="btn gradient-btn" style="padding: 0.5rem 1.25rem; font-size: 0.85rem;" {{ $user->hasUnpaidDebt() ? 'disabled' : '' }}>
                                    <i class="fa-solid fa-plus"></i> Fənni Seç
                                </button>
                            </form>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>
    </div>

@endsection
