@extends('layouts.portal')

@section('title', 'Müəllim Dərs Cədvəli — Xəzər Universiteti')
@section('header_title', 'Müəllim Həftəlik Cədvəl Grid-i')

@section('content')

    {{-- Top Banner --}}
    <div class="glass-panel" style="padding: 2rem; margin-bottom: 2rem; background: linear-gradient(135deg, rgba(6,17,36,0.95), rgba(0,43,73,0.85));">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
            <div>
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.6rem;">
                    <span class="badge badge-teacher"><i class="fa-solid fa-chalkboard-user"></i> {{ $user->name }}</span>
                    <span class="badge badge-primary"><i class="fa-solid fa-calendar-week"></i> {{ $termCode }}</span>
                    <span class="badge" style="background:rgba(200,157,53,0.15);color:#fbbf24;border:1px solid rgba(200,157,53,0.3);">
                        <i class="fa-solid fa-layer-group"></i> {{ $taughtSections->count() }} Fənn Bölməsi
                    </span>
                </div>
                <h2 class="font-heading" style="font-size: 1.8rem; color: #fff; margin-bottom: 0.25rem;">
                    Həftəlik Tədris Cədvəli
                </h2>
                <p style="color: #94a3b8; font-size: 0.9rem;">
                    Tədris etdiyiniz bütün fənn bölmələrinin günlər, saatlar və auditoriyalar üzrə interaktiv görünüşü.
                    Hər dərs kartından birbaşa LMS, Jurnal və Davamiyyət bölmələrinə keçid edə bilərsiniz.
                </p>
            </div>
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <button onclick="window.print()" class="btn btn-outline" style="font-size: 0.85rem;">
                    <i class="fa-solid fa-print"></i> Çap Et
                </button>
                <a href="{{ route('portal.dashboard') }}" class="btn btn-outline" style="font-size: 0.85rem;">
                    <i class="fa-solid fa-arrow-left"></i> Panelə Qayıt
                </a>
            </div>
        </div>
    </div>

    {{-- Live indicator if any section is ongoing right now --}}
    @php
        $now = now();
        $currentDay = $now->locale('az')->isoFormat('dddd');
        $currentTime = $now->format('H:i');
        $daysMap = [
            'Monday'    => 'Bazar ertəsi',
            'Tuesday'   => 'Çərşənbə axşamı',
            'Wednesday' => 'Çərşənbə',
            'Thursday'  => 'Cümə axşamı',
            'Friday'    => 'Cümə',
            'Saturday'  => 'Şənbə',
        ];
        $todayAz = $daysMap[$now->englishDayOfWeek] ?? '';
        $liveSection = $taughtSections->first(function($sec) use ($todayAz, $currentTime) {
            return $sec->day_of_week === $todayAz
                && $currentTime >= $sec->start_time
                && $currentTime <= $sec->end_time;
        });
    @endphp

    @if($liveSection)
        <div style="background: linear-gradient(90deg, rgba(16,185,129,0.15), rgba(16,185,129,0.05)); border: 1px solid rgba(16,185,129,0.35); border-radius: 0.85rem; padding: 1rem 1.5rem; margin-bottom: 1.75rem; display: flex; align-items: center; gap: 1rem;">
            <div style="width: 12px; height: 12px; background: #34d399; border-radius: 50%; animation: pulse 1.5s infinite; flex-shrink: 0;"></div>
            <div>
                <span style="font-weight: 700; color: #34d399; font-size: 0.95rem;">CANLI DƏRS:</span>
                <span style="color: #cbd5e1; margin-left: 0.4rem;">
                    <strong>{{ $liveSection->course->code }}</strong> — {{ $liveSection->course->name }}
                    ({{ $liveSection->start_time }} – {{ $liveSection->end_time }}, {{ $liveSection->room_number }})
                </span>
            </div>
            <div style="margin-left: auto; display: flex; gap: 0.65rem;">
                <a href="{{ route('portal.lms.course', $liveSection->id) }}" class="btn btn-primary" style="font-size: 0.78rem; padding: 0.35rem 0.85rem;">
                    <i class="fa-solid fa-book-open"></i> LMS Otağı
                </a>
                <a href="{{ route('portal.grading.show', $liveSection->id) }}" class="btn btn-outline" style="font-size: 0.78rem; padding: 0.35rem 0.85rem;">
                    <i class="fa-solid fa-clipboard-list"></i> Jurnal
                </a>
            </div>
        </div>
    @endif

    {{-- Weekly Grid --}}
    @php
        $days = [
            'Bazar ertəsi',
            'Çərşənbə axşamı',
            'Çərşənbə',
            'Cümə axşamı',
            'Cümə',
            'Şənbə',
        ];
        $dayIcons = [
            'Bazar ertəsi'       => 'fa-1',
            'Çərşənbə axşamı'   => 'fa-2',
            'Çərşənbə'           => 'fa-3',
            'Cümə axşamı'       => 'fa-4',
            'Cümə'               => 'fa-5',
            'Şənbə'              => 'fa-6',
        ];
        $scheduleByDay = [];
        foreach ($days as $d) {
            $scheduleByDay[$d] = $taughtSections->filter(fn($s) => $s->day_of_week === $d)
                                                ->sortBy('start_time');
        }
    @endphp

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
        @foreach($days as $day)
            @php
                $daySections = $scheduleByDay[$day];
                $isToday = ($day === $todayAz);
            @endphp
            <div class="glass-panel" style="padding: 1.5rem; border-top: 4px solid {{ $daySections->isNotEmpty() ? ($isToday ? '#34d399' : '#00A3E0') : 'rgba(255,255,255,0.08)' }}; transition: box-shadow 0.2s;">
                {{-- Day Header --}}
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                    <h3 class="font-heading" style="font-size: 1.1rem; color: {{ $isToday ? '#34d399' : '#fff' }}; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-regular fa-calendar-days" style="color: {{ $isToday ? '#34d399' : '#38bdf8' }};"></i>
                        {{ $day }}
                        @if($isToday)
                            <span style="font-size: 0.65rem; background: rgba(16,185,129,0.2); color: #34d399; border: 1px solid rgba(16,185,129,0.4); padding: 0.1rem 0.45rem; border-radius: 999px; font-weight: 700; letter-spacing: 0.05em;">BUGÜN</span>
                        @endif
                    </h3>
                    <span class="badge {{ $daySections->isNotEmpty() ? ($isToday ? 'badge-teacher' : 'badge-primary') : 'badge-outline' }}">
                        {{ $daySections->count() }} Dərs
                    </span>
                </div>

                {{-- Sections for the day --}}
                @if($daySections->isEmpty())
                    <div style="padding: 2rem 1rem; text-align: center; color: #475569;">
                        <i class="fa-regular fa-calendar-xmark" style="font-size: 2rem; opacity: 0.3; display: block; margin-bottom: 0.5rem;"></i>
                        <span style="font-size: 0.85rem;">Bu gün üçün tədris dərsi yoxdur</span>
                    </div>
                @else
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        @foreach($daySections as $sec)
                            @php
                                $isLive = $liveSection && $liveSection->id === $sec->id;
                                $enrolled = $sec->enrollments->count();
                                $capacity = $sec->capacity;
                                $fillPct = $capacity > 0 ? round($enrolled / $capacity * 100) : 0;
                            @endphp
                            <div class="glass-card" style="padding: 1.1rem 1.25rem; border-left: 3px solid {{ $isLive ? '#34d399' : '#38bdf8' }}; position: relative; overflow: hidden;">
                                {{-- Live badge --}}
                                @if($isLive)
                                    <div style="position: absolute; top: 0.6rem; right: 0.6rem; display: flex; align-items: center; gap: 0.3rem; font-size: 0.68rem; font-weight: 700; color: #34d399;">
                                        <div style="width: 7px; height: 7px; background: #34d399; border-radius: 50%; animation: pulse 1s infinite;"></div>
                                        CANLI
                                    </div>
                                @endif

                                {{-- Course code + time --}}
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <span class="font-mono badge badge-primary" style="font-size: 0.72rem;">{{ $sec->course->code }}</span>
                                    <span class="font-mono" style="font-size: 0.82rem; font-weight: 700; color: #38bdf8;">
                                        <i class="fa-regular fa-clock"></i> {{ $sec->start_time }} – {{ $sec->end_time }}
                                    </span>
                                </div>

                                {{-- Course name --}}
                                <div style="font-weight: 700; color: #fff; font-size: 0.97rem; margin-bottom: 0.5rem; line-height: 1.3;">
                                    {{ $sec->course->name }}
                                </div>

                                {{-- Room & Program --}}
                                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; color: #94a3b8; margin-bottom: 0.75rem;">
                                    <span>
                                        <i class="fa-solid fa-door-open text-amber-400"></i> {{ $sec->room_number }}
                                        &nbsp;•&nbsp;
                                        <i class="fa-solid fa-graduation-cap text-purple-400"></i> {{ $sec->course->program->name ?? 'Proqram' }}
                                    </span>
                                    <span style="color: #38bdf8; font-weight: 600;">{{ $sec->course->credits }} ECTS</span>
                                </div>

                                {{-- Student fill bar --}}
                                <div style="margin-bottom: 0.9rem;">
                                    <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem;">
                                        <span><i class="fa-solid fa-users"></i> {{ $enrolled }} / {{ $capacity }} tələbə</span>
                                        <span style="color: {{ $fillPct >= 90 ? '#f87171' : ($fillPct >= 70 ? '#fbbf24' : '#34d399') }};">{{ $fillPct }}%</span>
                                    </div>
                                    <div style="height: 4px; background: rgba(255,255,255,0.08); border-radius: 999px; overflow: hidden;">
                                        <div style="height: 100%; width: {{ $fillPct }}%; background: {{ $fillPct >= 90 ? '#f87171' : ($fillPct >= 70 ? '#fbbf24' : '#34d399') }}; border-radius: 999px; transition: width 0.5s;"></div>
                                    </div>
                                </div>

                                {{-- Action buttons --}}
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                    <a href="{{ route('portal.lms.course', $sec->id) }}"
                                       class="btn btn-primary"
                                       style="font-size: 0.75rem; padding: 0.35rem 0.75rem; flex: 1; justify-content: center;">
                                        <i class="fa-solid fa-book-open"></i> LMS Otağı
                                    </a>
                                    <a href="{{ route('portal.grading.show', $sec->id) }}"
                                       class="btn btn-outline"
                                       style="font-size: 0.75rem; padding: 0.35rem 0.75rem; flex: 1; justify-content: center;">
                                        <i class="fa-solid fa-clipboard-check"></i> Jurnal
                                    </a>
                                    <a href="{{ route('portal.grading.analytics', $sec->id) }}"
                                       class="btn btn-outline"
                                       style="font-size: 0.75rem; padding: 0.35rem 0.75rem; flex: 1; justify-content: center; border-color: rgba(168,85,247,0.35); color: #c084fc;">
                                        <i class="fa-solid fa-chart-pie"></i> Analitika
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- All Sections Summary Table --}}
    <div class="glass-panel" style="padding: 2rem; margin-bottom: 2rem;">
        <h3 class="font-heading" style="font-size: 1.3rem; color: #fff; margin-bottom: 1.25rem;">
            <i class="fa-solid fa-table-list text-sky-400"></i> Bütün Fənn Bölmələri — Cədvəl Xülasəsi
        </h3>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; text-align: left;">
                <thead>
                    <tr style="color: #64748b; border-bottom: 1px solid rgba(255,255,255,0.08);">
                        <th style="padding: 0.7rem 0.75rem;">KOD</th>
                        <th style="padding: 0.7rem 0.75rem;">FƏNN ADI</th>
                        <th style="padding: 0.7rem 0.75rem;">GÜN</th>
                        <th style="padding: 0.7rem 0.75rem;">VAXT</th>
                        <th style="padding: 0.7rem 0.75rem;">OTAQʼ</th>
                        <th style="padding: 0.7rem 0.75rem;">TƏLƏBƏʼ</th>
                        <th style="padding: 0.7rem 0.75rem;">ECTS</th>
                        <th style="padding: 0.7rem 0.75rem;">ƏMƏLİYYAT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($taughtSections->sortBy('day_of_week') as $sec)
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.04); transition: background 0.15s;" onmouseover="this.style.background='rgba(0,163,224,0.05)'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 0.75rem; font-family: 'JetBrains Mono', monospace; color: #38bdf8; font-weight: 600;">{{ $sec->course->code }}</td>
                            <td style="padding: 0.75rem; color: #fff; font-weight: 600;">{{ $sec->course->name }}</td>
                            <td style="padding: 0.75rem; color: #94a3b8;">{{ $sec->day_of_week }}</td>
                            <td style="padding: 0.75rem; color: #38bdf8; font-family: 'JetBrains Mono', monospace;">{{ $sec->start_time }} – {{ $sec->end_time }}</td>
                            <td style="padding: 0.75rem; color: #fbbf24;">{{ $sec->room_number }}</td>
                            <td style="padding: 0.75rem; color: #34d399;">{{ $sec->enrollments->count() }} / {{ $sec->capacity }}</td>
                            <td style="padding: 0.75rem; color: #c084fc;">{{ $sec->course->credits }}</td>
                            <td style="padding: 0.75rem;">
                                <div style="display: flex; gap: 0.4rem;">
                                    <a href="{{ route('portal.lms.course', $sec->id) }}" class="btn btn-primary" style="font-size: 0.72rem; padding: 0.28rem 0.65rem;">
                                        <i class="fa-solid fa-book-open"></i> LMS
                                    </a>
                                    <a href="{{ route('portal.grading.show', $sec->id) }}" class="btn btn-outline" style="font-size: 0.72rem; padding: 0.28rem 0.65rem;">
                                        <i class="fa-solid fa-clipboard-check"></i>
                                    </a>
                                    <a href="{{ route('portal.grading.analytics', $sec->id) }}" class="btn btn-outline" style="font-size: 0.72rem; padding: 0.28rem 0.65rem; border-color: rgba(168,85,247,0.35); color: #c084fc;">
                                        <i class="fa-solid fa-chart-pie"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection

@section('scripts')
<style>
@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%       { opacity: 0.5; transform: scale(1.3); }
}
@media print {
    .portal-sidebar, header, .btn { display: none !important; }
    .portal-content { margin-left: 0 !important; }
}
</style>
@endsection
