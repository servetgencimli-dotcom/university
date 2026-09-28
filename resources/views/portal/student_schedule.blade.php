@extends('layouts.portal')

@section('title', 'Dərs Cədvəli — Xəzər Universiteti')
@section('header_title', 'Həftəlik Dərs Cədvəli')

@section('content')

    <!-- Top Banner -->
    <div class="glass-panel" style="padding: 2rem; margin-bottom: 2rem; background: linear-gradient(135deg, rgba(6, 17, 36, 0.9), rgba(12, 24, 44, 0.95));">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
            <div>
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                    <span class="badge badge-student"><i class="fa-solid fa-graduation-cap"></i> {{ $user->name }}</span>
                    <span class="badge badge-primary"><i class="fa-solid fa-calendar"></i> {{ $termCode }}</span>
                </div>
                <h2 class="font-heading" style="font-size: 1.8rem; color: #fff; margin-bottom: 0.25rem;">Həftəlik Akademik Dərs Cədvəli</h2>
                <p style="color: #94a3b8; font-size: 0.9rem;">
                    Bütün mühazirə, məşğələ və laboratoriya dərslərinizin günlər, saatlar və auditoriyalar üzrə qrafiki.
                </p>
            </div>

            <div>
                <button onclick="window.print()" class="btn btn-outline" style="font-size: 0.85rem;">
                    <i class="fa-solid fa-print"></i> Cədvəli Çap Et
                </button>
            </div>
        </div>
    </div>

    <!-- Days Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;">
        @foreach($daysOfWeek as $day)
            @php
                $dayClasses = $scheduleByDay[$day] ?? collect();
            @endphp
            <div class="glass-panel" style="padding: 1.5rem; border-top: 4px solid {{ $dayClasses->isNotEmpty() ? '#00A3E0' : 'rgba(255,255,255,0.1)' }};">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                    <h3 class="font-heading" style="font-size: 1.15rem; color: #fff; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-regular fa-calendar-days text-sky-400"></i> {{ $day }}
                    </h3>
                    <span class="badge {{ $dayClasses->isNotEmpty() ? 'badge-primary' : 'badge-outline' }}">
                        {{ $dayClasses->count() }} Dərs
                    </span>
                </div>

                @if($dayClasses->isEmpty())
                    <div style="padding: 2rem 1rem; text-align: center; color: #64748b; font-size: 0.85rem;">
                        <i class="fa-regular fa-calendar-xmark text-2xl mb-2 block opacity-40"></i>
                        Bu gün üçün tədris dərsi yoxdur
                    </div>
                @else
                    <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                        @foreach($dayClasses as $enr)
                            @php
                                $sec = $enr->courseSection;
                                $c = $sec?->course;
                            @endphp
                            <div class="glass-card" style="padding: 1rem; border-left: 3px solid #38bdf8;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                                    <span class="font-mono badge badge-primary" style="font-size: 0.72rem;">{{ $c?->code }}</span>
                                    <span class="font-mono" style="font-size: 0.82rem; font-weight: 700; color: #38bdf8;">
                                        <i class="fa-regular fa-clock"></i> {{ $sec?->start_time }} - {{ $sec?->end_time }}
                                    </span>
                                </div>
                                <div style="font-weight: 700; color: #fff; font-size: 0.95rem; margin-bottom: 0.4rem;">{{ $c?->name }}</div>
                                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; color: #94a3b8;">
                                    <span><i class="fa-solid fa-chalkboard-user text-emerald-400"></i> {{ $sec?->teacher?->name ?? 'Professor' }}</span>
                                    <span class="badge badge-outline" style="background: rgba(255,255,255,0.05); color: #cbd5e1;">
                                        <i class="fa-solid fa-door-open text-amber-400"></i> {{ $sec?->room_number }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>

@endsection
