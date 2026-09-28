@extends('layouts.portal')

@section('title', 'Kursun Davamı Məlumatı — Xəzər Universiteti')
@section('header_title', 'Kursun Davamı Məlumatı & Davamiyyət Jurnalı')

@section('content')

    <!-- Top Banner -->
    <div class="glass-panel" style="padding: 2rem; margin-bottom: 2rem; background: linear-gradient(135deg, rgba(6, 17, 36, 0.9), rgba(12, 24, 44, 0.95));">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
            <div>
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                    <span class="badge badge-student"><i class="fa-solid fa-graduation-cap"></i> {{ $user->name }}</span>
                    <span class="badge badge-primary"><i class="fa-solid fa-user-check"></i> Kursun Davamiyyəti</span>
                </div>
                <h2 class="font-heading" style="font-size: 1.8rem; color: #fff; margin-bottom: 0.25rem;">Kursun Davamı və Qayıb Statistikası</h2>
                <p style="color: #94a3b8; font-size: 0.9rem;">
                    Fənlər üzrə dərsdə iştirak faizi, qayıbların sayı və maksimal 25% qayıb limiti monitorinqi.
                </p>
            </div>

            <div class="glass-card" style="padding: 1rem 1.25rem; border-color: rgba(239, 68, 68, 0.3); background: rgba(239, 68, 68, 0.08);">
                <div style="font-size: 0.75rem; color: #f87171; font-weight: 700; text-transform: uppercase;">
                    <i class="fa-solid fa-triangle-exclamation"></i> Universitet Qaydası
                </div>
                <div style="font-size: 0.85rem; color: #fca5a5; margin-top: 0.25rem;">
                    Qayıb limiti <strong>25%-i</strong> keçdikdə tələbə imtahana buraxılmır.
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Cards per Course -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem; margin-bottom: 2.5rem;">
        @forelse($enrollments as $enr)
            @php
                $sec = $enr->courseSection;
                $course = $sec?->course;
                $pct = (float)$enr->attendance_percentage;
                $records = $enr->attendanceRecords;
                $totalSessions = $records->count();
                $presentCount = $records->where('status', 'present')->count();
                $lateCount = $records->where('status', 'late')->count();
                $excusedCount = $records->where('status', 'excused')->count();
                $absentCount = $records->where('status', 'absent')->count();
                $absenceRate = $totalSessions > 0 ? round(($absentCount / $totalSessions) * 100, 1) : 0;
            @endphp
            <div class="glass-panel" style="padding: 1.75rem;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.06); padding-bottom: 1rem;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.35rem;">
                            <span class="font-mono badge badge-primary">{{ $course?->code }}</span>
                            <span style="font-size: 0.85rem; color: #94a3b8;">{{ $sec?->day_of_week }} • {{ $sec?->start_time }} - {{ $sec?->end_time }}</span>
                        </div>
                        <h3 class="font-heading" style="font-size: 1.35rem; color: #fff;">{{ $course?->name }}</h3>
                        <div style="font-size: 0.85rem; color: #cbd5e1; margin-top: 0.25rem;">
                            <i class="fa-solid fa-chalkboard-user text-emerald-400"></i> Müəllim: <strong>{{ $sec?->teacher?->name ?? 'Professor' }}</strong>
                        </div>
                    </div>

                    <!-- Status indicators -->
                    <div style="display: flex; align-items: center; gap: 1.5rem; text-align: right;">
                        <div>
                            <div style="font-size: 0.75rem; color: #94a3b8;">İştirak Faizi</div>
                            <div class="font-heading" style="font-size: 2rem; color: {{ $pct >= 85 ? '#34d399' : ($pct >= 75 ? '#fbbf24' : '#f87171') }};">
                                {{ $pct }}%
                            </div>
                        </div>

                        <div style="padding-left: 1.5rem; border-left: 1px solid rgba(255,255,255,0.1);">
                            <div style="font-size: 0.75rem; color: #94a3b8;">Status</div>
                            <div style="margin-top: 0.25rem;">
                                @if($pct >= 85)
                                    <span class="badge badge-success"><i class="fa-solid fa-check"></i> Əla (Təhlükəsiz)</span>
                                @elseif($pct >= 75)
                                    <span class="badge badge-warning"><i class="fa-solid fa-triangle-exclamation"></i> Diqqət (Normal)</span>
                                @else
                                    <span class="badge badge-danger"><i class="fa-solid fa-ban"></i> Limit Aşıldı (Kəsr)</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Session summary grid -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 0.75rem; margin-bottom: 1.25rem;">
                    <div class="glass-card" style="padding: 0.75rem; text-align: center;">
                        <div style="font-size: 0.7rem; color: #94a3b8;">Qeydə Alınan Dərs</div>
                        <div class="font-heading" style="font-size: 1.2rem; color: #fff;">{{ $totalSessions }}</div>
                    </div>
                    <div class="glass-card" style="padding: 0.75rem; text-align: center; border-color: rgba(16, 185, 129, 0.3);">
                        <div style="font-size: 0.7rem; color: #34d399;">İştirak Etdi</div>
                        <div class="font-heading" style="font-size: 1.2rem; color: #34d399;">{{ $presentCount }}</div>
                    </div>
                    <div class="glass-card" style="padding: 0.75rem; text-align: center; border-color: rgba(245, 158, 11, 0.3);">
                        <div style="font-size: 0.7rem; color: #fbbf24;">Gecikmə</div>
                        <div class="font-heading" style="font-size: 1.2rem; color: #fbbf24;">{{ $lateCount }}</div>
                    </div>
                    <div class="glass-card" style="padding: 0.75rem; text-align: center; border-color: rgba(56, 189, 248, 0.3);">
                        <div style="font-size: 0.7rem; color: #38bdf8;">Üzrlü Səbəb</div>
                        <div class="font-heading" style="font-size: 1.2rem; color: #38bdf8;">{{ $excusedCount }}</div>
                    </div>
                    <div class="glass-card" style="padding: 0.75rem; text-align: center; border-color: rgba(239, 68, 68, 0.3);">
                        <div style="font-size: 0.7rem; color: #f87171;">Qayıb (İştirak Etmədi)</div>
                        <div class="font-heading" style="font-size: 1.2rem; color: #f87171;">{{ $absentCount }}</div>
                    </div>
                </div>

                <!-- Session logs collapse / list -->
                @if($records->isNotEmpty())
                    <details style="background: rgba(0,0,0,0.2); border-radius: 0.5rem; padding: 0.75rem; cursor: pointer;">
                        <summary style="font-size: 0.85rem; font-weight: 600; color: #38bdf8;">
                            <i class="fa-solid fa-list-ul"></i> Bütün dərslər üzrə fərdi qeydiyyat tarixçəsinə bax ({{ $totalSessions }} dərs)
                        </summary>
                        <div style="margin-top: 0.75rem; overflow-x: auto;">
                            <table style="width: 100%; font-size: 0.82rem; border-collapse: collapse;">
                                <thead>
                                    <tr style="color: #94a3b8; border-bottom: 1px solid rgba(255,255,255,0.08); text-align: left;">
                                        <th style="padding: 0.4rem 0.6rem;">Tarix</th>
                                        <th style="padding: 0.4rem 0.6rem;">Status</th>
                                        <th style="padding: 0.4rem 0.6rem;">Müəllimin Qeydi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($records as $rec)
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                                            <td class="font-mono" style="padding: 0.4rem 0.6rem; color: #cbd5e1;">{{ \Carbon\Carbon::parse($rec->session_date)->format('d.m.Y') }}</td>
                                            <td style="padding: 0.4rem 0.6rem;">
                                                @if($rec->status === 'present')
                                                    <span class="badge badge-success">İştirak etdi</span>
                                                @elseif($rec->status === 'late')
                                                    <span class="badge badge-warning">Gecikdi</span>
                                                @elseif($rec->status === 'excused')
                                                    <span class="badge badge-primary">Üzrlü</span>
                                                @else
                                                    <span class="badge badge-danger">Qayıb</span>
                                                @endif
                                            </td>
                                            <td style="padding: 0.4rem 0.6rem; color: #94a3b8;">{{ $rec->notes ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </details>
                @endif
            </div>
        @empty
            <div class="glass-panel" style="padding: 3rem; text-align: center; color: #94a3b8;">
                <i class="fa-solid fa-user-check text-4xl mb-3 text-slate-500"></i>
                <div style="font-size: 1.1rem; color: #fff; font-weight: 600;">Davamiyyət məlumatı tapılmadı.</div>
            </div>
        @endforelse
    </div>

@endsection
