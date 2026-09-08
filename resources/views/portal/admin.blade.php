@extends('layouts.portal')

@section('title', 'Fakültə Dekanlığı & İdarəetmə Paneli')
@section('header_title', 'Fakültə İdarəetmə Paneli')

@section('content')

    <!-- Top Stats -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2.5rem;">
        <div class="glass-card" style="padding: 1.5rem; border-left: 4px solid #f59e0b;">
            <div style="font-size: 0.8rem; color: #94a3b8; font-weight: 600;">FAKÜLTƏ TƏLƏBƏLƏRİ</div>
            <div class="font-heading" style="font-size: 2rem; color: #fff;">{{ $stats['faculty_students'] }}</div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.3rem;">Bakalavr & Magistr</div>
        </div>
        <div class="glass-card" style="padding: 1.5rem; border-left: 4px solid #10b981;">
            <div style="font-size: 0.8rem; color: #94a3b8; font-weight: 600;">PROFESSOR-MÜƏLLİM HEYƏTİ</div>
            <div class="font-heading" style="font-size: 2rem; color: #fff;">{{ $stats['faculty_teachers'] }}</div>
            <div style="font-size: 0.75rem; color: #34d399; margin-top: 0.3rem;">Akademik heyət</div>
        </div>
        <div class="glass-card" style="padding: 1.5rem; border-left: 4px solid #6366f1;">
            <div style="font-size: 0.8rem; color: #94a3b8; font-weight: 600;">GÖZLƏYƏN ƏRİZƏLƏR</div>
            <div class="font-heading" style="font-size: 2rem; color: #fff;">{{ $stats['pending_applications'] }}</div>
            <div style="font-size: 0.75rem; color: #fbbf24; margin-top: 0.3rem;">Baxılmalı müraciətlər</div>
        </div>
    </div>

    <!-- Faculty Programs -->
    <div class="glass-panel" style="padding: 2rem; margin-bottom: 2.5rem;">
        <h3 class="font-heading" style="font-size: 1.3rem; margin-bottom: 1.25rem; color: #fff;">
            <i class="fa-solid fa-graduation-cap text-amber-400"></i> Fakültənin Tədris İxtisasları ({{ $programs->count() }})
        </h3>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem;">
            @foreach($programs as $p)
                <div class="glass-card" style="padding: 1.25rem;">
                    <span class="badge badge-accent" style="margin-bottom: 0.4rem;">{{ strtoupper($p->degree_level) }}</span>
                    <h4 class="font-heading" style="font-size: 1.1rem; margin-bottom: 0.4rem;">{{ $p->name }}</h4>
                    <div style="font-size: 0.85rem; color: #38bdf8; font-weight: 700;">{{ number_format($p->tuition_fee_azn) }} ₼ / il</div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Admissions List -->
    <div class="glass-panel" style="padding: 2rem;">
        <h3 class="font-heading" style="font-size: 1.3rem; margin-bottom: 1.25rem; color: #fff;">
            <i class="fa-solid fa-inbox text-indigo-400"></i> Fakültəyə Daxil Olan Son Qəbul Müraciətləri
        </h3>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.1); color: #94a3b8;">
                        <th style="padding: 0.75rem 1rem;">NÖMRƏ</th>
                        <th style="padding: 0.75rem 1rem;">NAMİZƏD</th>
                        <th style="padding: 0.75rem 1rem;">İXTİSAS</th>
                        <th style="padding: 0.75rem 1rem;">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($applications as $app)
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                            <td class="font-mono" style="padding: 0.85rem 1rem; color: #818cf8; font-weight: 700;">{{ $app->application_number }}</td>
                            <td style="padding: 0.85rem 1rem; color: #fff; font-weight: 600;">{{ $app->full_name }}</td>
                            <td style="padding: 0.85rem 1rem; color: #cbd5e1;">{{ $app->program->name }}</td>
                            <td style="padding: 0.85rem 1rem;"><span class="badge badge-primary">{{ $app->status }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
