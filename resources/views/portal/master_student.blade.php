@extends('layouts.portal')

@section('title', 'Magistrant & Tədqiqatçı Kabineti')
@section('header_title', 'Magistratura və Tədqiqat Portalı')

@section('content')

    <!-- Top Master Summary -->
    <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 2rem; margin-bottom: 2.5rem;">
        
        <div class="glass-panel" style="padding: 2rem; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <div>
                        <span class="badge badge-master" style="margin-bottom: 0.35rem;">Magistrant & Tədqiqatçı</span>
                        <h2 class="font-heading" style="font-size: 1.8rem; color: #fff;">{{ $user->name }}</h2>
                        <div style="font-size: 0.9rem; color: #38bdf8;">{{ $user->faculty->name ?? 'İT Fakültəsi' }} • Generativ AI Magistr Proqramı</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 0.75rem; color: #94a3b8;">ÜOMG (GPA)</div>
                        <div class="font-heading" style="font-size: 2.5rem; color: #a855f7; line-height: 1;">{{ $user->gpa }}</div>
                    </div>
                </div>

                <p style="color: #94a3b8; font-size: 0.9rem; line-height: 1.6;">
                    {{ $user->bio }}
                </p>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.06);">
                <span class="badge badge-accent"><i class="fa-solid fa-microchip"></i> NVIDIA GPU Lab Girişi: Aktiv</span>
                <span class="badge badge-success"><i class="fa-solid fa-book"></i> IEEE Digital Library Pro Access</span>
            </div>
        </div>

        <!-- Master Thesis Progress Card -->
        @if($thesis)
            <div id="thesis-tracker" class="glass-panel" style="padding: 2rem; border-color: rgba(168, 85, 247, 0.4); background: linear-gradient(135deg, rgba(30, 27, 75, 0.8), rgba(15, 23, 42, 0.9));">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <span class="badge badge-master"><i class="fa-solid fa-microscope"></i> Magistr Dissertasiyası</span>
                    <span style="font-weight: 700; color: #c084fc; font-size: 1.1rem;">{{ $thesis->progress_percentage }}%</span>
                </div>

                <h3 class="font-heading" style="font-size: 1.15rem; color: #fff; margin-bottom: 0.75rem; line-height: 1.4;">
                    {{ $thesis->title }}
                </h3>

                <div style="font-size: 0.85rem; color: #cbd5e1; margin-bottom: 1.25rem;">
                    <div><strong>Elmi Rəhbər:</strong> {{ $thesis->advisor->name ?? 'Professor' }}</div>
                    <div style="color: #94a3b8;">Müdafiə tarixi: {{ $thesis->expected_defense_date?->format('d F Y') }}</div>
                </div>

                <!-- Progress Bar -->
                <div style="background: rgba(255,255,255,0.1); border-radius: 9999px; height: 10px; overflow: hidden; margin-bottom: 0.5rem;">
                    <div style="background: linear-gradient(90deg, #a855f7, #6366f1); height: 100%; width: {{ $thesis->progress_percentage }}%;"></div>
                </div>
                <div style="font-size: 0.75rem; color: #94a3b8; text-align: right;">Mərhələ: {{ strtoupper($thesis->stage) }}</div>
            </div>
        @endif

    </div>

    <!-- Master Courses -->
    <div class="glass-panel" style="padding: 2rem;">
        <h3 class="font-heading" style="font-size: 1.35rem; color: #fff; margin-bottom: 1.25rem;">
            <i class="fa-solid fa-book-open text-purple-400"></i> Cari Elmi-Tədqiqat və İxtisas Fənləri
        </h3>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.1); color: #94a3b8;">
                        <th style="padding: 0.75rem 1rem;">FƏNN KODU</th>
                        <th style="padding: 0.75rem 1rem;">FƏNN ADI</th>
                        <th style="padding: 0.75rem 1rem;">MÜƏLLİM</th>
                        <th style="padding: 0.75rem 1rem;">KREDİT</th>
                        <th style="padding: 0.75rem 1rem;">YEKUN BAL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($enrollments as $enr)
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                            <td class="font-mono" style="padding: 1rem; color: #c084fc; font-weight: 700;">{{ $enr->courseSection->course->code }}</td>
                            <td style="padding: 1rem; font-weight: 600; color: #fff;">{{ $enr->courseSection->course->name }}</td>
                            <td style="padding: 1rem; color: #cbd5e1;">{{ $enr->courseSection->teacher->name ?? 'Professor' }}</td>
                            <td style="padding: 1rem;">{{ $enr->courseSection->course->credits }} ECTS</td>
                            <td style="padding: 1rem; font-weight: 700; color: #34d399; font-size: 1.05rem;">{{ $enr->total_score }} ({{ $enr->letter_grade }})</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
