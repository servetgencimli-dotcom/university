@extends('layouts.portal')

@section('title', 'Tələbə Şəxsi Kabineti')
@section('header_title', 'Tələbə Portalı & Elektron Kabinet')

@section('content')

    <!-- Top Student Summary & Digital Student ID Card -->
    <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 2rem; margin-bottom: 2.5rem; align-items: stretch;">
        
        <!-- Left: Academic Overview -->
        <div class="glass-panel" style="padding: 2rem; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                    <div>
                        <span class="badge badge-student" style="margin-bottom: 0.35rem;">Bakalavr Tələbəsi</span>
                        <h2 class="font-heading" style="font-size: 1.8rem; color: #fff;">{{ $user->name }}</h2>
                        <div style="font-size: 0.9rem; color: #38bdf8;">{{ $user->faculty->name ?? 'İT Fakültəsi' }}</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 0.75rem; color: #94a3b8;">ÜOMG (GPA)</div>
                        <div class="font-heading" style="font-size: 2.5rem; color: #34d399; line-height: 1;">{{ $user->gpa }}</div>
                    </div>
                </div>

                <p style="color: #94a3b8; font-size: 0.9rem; line-height: 1.6; margin-bottom: 1.5rem;">
                    {{ $user->bio }}
                </p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; padding-top: 1.25rem; border-top: 1px solid rgba(255,255,255,0.06); text-align: center;">
                <div>
                    <div style="font-size: 0.75rem; color: #94a3b8;">Qazanılmış Kredit</div>
                    <div class="font-heading" style="font-size: 1.3rem; color: #fff;">{{ $user->earned_credits }} ECTS</div>
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: #94a3b8;">Qəbul İli</div>
                    <div class="font-heading" style="font-size: 1.3rem; color: #fff;">{{ $user->admission_year }}</div>
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: #94a3b8;">Status</div>
                    <div class="font-heading" style="font-size: 1.1rem; color: #34d399;">Aktiv</div>
                </div>
            </div>
        </div>

        <!-- Right: DIGITAL STUDENT ID CARD -->
        <div id="idcard-section" class="glass-panel" style="padding: 2rem; background: linear-gradient(135deg, rgba(30, 27, 75, 0.9), rgba(15, 23, 42, 0.95)); border-color: rgba(99, 102, 241, 0.4); display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <div style="font-size: 0.8rem; font-weight: 700; color: #818cf8; letter-spacing: 0.05em; text-transform: uppercase;">RƏQƏMSAL TƏLƏBƏ KARTI</div>
                    <i class="fa-solid fa-wifi" style="transform: rotate(90deg); color: #38bdf8;"></i>
                </div>

                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <div style="width: 54px; height: 54px; background: rgba(99, 102, 241, 0.2); border: 2px solid #818cf8; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: #818cf8;">
                        <i class="fa-solid fa-user-graduate"></i>
                    </div>
                    <div>
                        <div class="font-heading" style="font-size: 1.2rem; color: #fff;">{{ $user->name }}</div>
                        <div class="font-mono" style="font-size: 0.85rem; color: #38bdf8; font-weight: 700;">ID: {{ $user->student_id }}</div>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: flex-end; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.08);">
                <div>
                    <div style="font-size: 0.7rem; color: #94a3b8;">Universitet:</div>
                    <div style="font-size: 0.85rem; font-weight: 600; color: #fff;">Xəzər Universiteti</div>
                </div>
                <div style="background: #fff; padding: 0.35rem; border-radius: 0.4rem;">
                    <i class="fa-solid fa-qrcode" style="font-size: 2.2rem; color: #000;"></i>
                </div>
            </div>
        </div>

    </div>

    <!-- Section: Current Semester Courses & Grades -->
    <div id="grades-section" class="glass-panel" style="padding: 2rem; margin-bottom: 2.5rem;">
        <h3 class="font-heading" style="font-size: 1.35rem; color: #fff; margin-bottom: 1.25rem;">
            <i class="fa-solid fa-book-open text-indigo-400"></i> Cari Semestr Fənləri və Ballarım
        </h3>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.1); color: #94a3b8;">
                        <th style="padding: 0.75rem 1rem;">FƏNN KODU</th>
                        <th style="padding: 0.75rem 1rem;">FƏNN ADI</th>
                        <th style="padding: 0.75rem 1rem;">MÜƏLLİM</th>
                        <th style="padding: 0.75rem 1rem;">KREDİT</th>
                        <th style="padding: 0.75rem 1rem;">ARALIQ (50)</th>
                        <th style="padding: 0.75rem 1rem;">FINAL (50)</th>
                        <th style="padding: 0.75rem 1rem;">YEKUN</th>
                        <th style="padding: 0.75rem 1rem;">HƏRFİ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($enrollments as $enr)
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                            <td class="font-mono" style="padding: 1rem; color: #818cf8; font-weight: 700;">{{ $enr->courseSection->course->code }}</td>
                            <td style="padding: 1rem; font-weight: 600; color: #fff;">{{ $enr->courseSection->course->name }}</td>
                            <td style="padding: 1rem; color: #cbd5e1;">{{ $enr->courseSection->teacher->name ?? 'Professor' }}</td>
                            <td style="padding: 1rem;">{{ $enr->courseSection->course->credits }} ECTS</td>
                            <td style="padding: 1rem; font-weight: 600; color: #fff;">{{ $enr->midterm_score }}</td>
                            <td style="padding: 1rem; font-weight: 600; color: #fff;">{{ $enr->final_score }}</td>
                            <td style="padding: 1rem; font-weight: 700; color: #38bdf8; font-size: 1.05rem;">{{ $enr->total_score }}</td>
                            <td style="padding: 1rem;">
                                <span class="badge {{ $enr->letter_grade === 'A' ? 'badge-success' : 'badge-primary' }}">
                                    {{ $enr->letter_grade }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section: Weekly Schedule & Invoices -->
    <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 2rem;">
        
        <!-- Schedule -->
        <div id="schedule-section" class="glass-panel" style="padding: 2rem;">
            <h3 class="font-heading" style="font-size: 1.35rem; color: #fff; margin-bottom: 1.25rem;">
                <i class="fa-solid fa-calendar-week text-cyan-400"></i> Həftəlik Dərs Cədvəli
            </h3>
            <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                @foreach($enrollments as $enr)
                    <div class="glass-card" style="padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-weight: 700; color: #fff; font-size: 0.95rem;">{{ $enr->courseSection->course->name }}</div>
                            <div style="font-size: 0.8rem; color: #94a3b8;">{{ $enr->courseSection->day_of_week }} • {{ $enr->courseSection->room_number }}</div>
                        </div>
                        <span class="font-mono badge badge-primary">
                            {{ $enr->courseSection->start_time }} - {{ $enr->courseSection->end_time }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Invoices -->
        <div class="glass-panel" style="padding: 2rem;">
            <h3 class="font-heading" style="font-size: 1.35rem; color: #fff; margin-bottom: 1.25rem;">
                <i class="fa-solid fa-receipt text-emerald-400"></i> Təhsil Haqqı Fakturaları
            </h3>
            <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                @foreach($invoices as $inv)
                    <div class="glass-card" style="padding: 1rem 1.25rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                            <span class="font-mono" style="font-size: 0.85rem; color: #818cf8; font-weight: 700;">{{ $inv->invoice_number }}</span>
                            @if($inv->status === 'paid')
                                <span class="badge badge-success"><i class="fa-solid fa-check"></i> Ödənilib</span>
                            @elseif($inv->status === 'overdue')
                                <span class="badge badge-danger"><i class="fa-solid fa-triangle-exclamation"></i> Gecikib</span>
                            @else
                                <span class="badge badge-warning"><i class="fa-solid fa-clock"></i> Ödənilməyib</span>
                            @endif
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem;">
                            <span style="color: #94a3b8;">{{ $inv->semester_term }}</span>
                            <span style="font-weight: 700; color: #fff; font-size: 1.1rem;">{{ number_format($inv->amount) }} ₼</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

@endsection
