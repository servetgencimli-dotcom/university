@extends('layouts.portal')

@section('title', 'Abituriyent Şəxsi Kabineti')
@section('header_title', 'Elektron Qəbul Müraciətim')

@section('content')

    <div class="glass-panel" style="padding: 2.5rem; max-width: 900px; margin-bottom: 2.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div>
                <span class="badge badge-applicant" style="margin-bottom: 0.35rem;">Qəbul Namizədi</span>
                <h2 class="font-heading" style="font-size: 1.8rem; color: #fff;">{{ $user->name }}</h2>
                <div style="font-size: 0.9rem; color: #94a3b8;">{{ $user->email }} • {{ $user->phone }}</div>
            </div>
            <a href="{{ route('admissions.apply') }}" class="btn gradient-btn">
                <i class="fa-solid fa-plus"></i> Yeni Müraciət Et
            </a>
        </div>

        @if($applications->isEmpty())
            <div style="text-align: center; padding: 2rem; color: #94a3b8;">
                Hələ heç bir ixtisasa müraciət etməmisiniz.
            </div>
        @else
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                @foreach($applications as $app)
                    <div class="glass-card" style="padding: 1.75rem; border-color: rgba(6, 182, 212, 0.3);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.06); padding-bottom: 0.75rem;">
                            <div>
                                <span class="font-mono" style="font-size: 0.9rem; color: #818cf8; font-weight: 700;">{{ $app->application_number }}</span>
                                <span style="font-size: 0.8rem; color: #94a3b8; margin-left: 0.5rem;">({{ $app->created_at->format('d.m.Y H:i') }})</span>
                            </div>
                            <span class="badge badge-primary">{{ strtoupper($app->status) }}</span>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.9rem; margin-bottom: 1.25rem;">
                            <div><span style="color: #94a3b8;">Seçilmiş İxtisas:</span> <strong style="color: #38bdf8;">{{ $app->program->name }}</strong></div>
                            <div><span style="color: #94a3b8;">Fakültə:</span> <strong style="color: #fff;">{{ $app->program->department->faculty->name }}</strong></div>
                            <div><span style="color: #94a3b8;">İmtahan Balı:</span> <strong style="color: #fff;">{{ $app->exam_score ?? 'N/A' }} ({{ $app->exam_type }})</strong></div>
                            <div><span style="color: #94a3b8;">Attestat GPA:</span> <strong style="color: #fff;">{{ $app->high_school_gpa ?? 'N/A' }}</strong></div>
                        </div>

                        @if($app->reviewer_notes)
                            <div style="background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.2); padding: 0.85rem; border-radius: 0.5rem; font-size: 0.85rem; color: #cbd5e1; margin-bottom: 1rem;">
                                <strong style="color: #818cf8;"><i class="fa-solid fa-comment"></i> Komissiya Rəyi:</strong> {{ $app->reviewer_notes }}
                            </div>
                        @endif

                        <div style="display: flex; justify-content: flex-end;">
                            <a href="{{ route('admissions.track', ['app_number' => $app->application_number]) }}" class="btn btn-outline" style="padding: 0.4rem 0.85rem; font-size: 0.85rem;">
                                <i class="fa-solid fa-timeline"></i> Canlı İzləmə Səhifəsi
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

@endsection
