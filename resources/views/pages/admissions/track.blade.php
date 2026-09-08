@extends('layouts.app')

@section('title', 'Müraciət Statusunu Yoxla — Xəzər Universiteti')

@section('content')

    <!-- Header Banner -->
    <div style="background: radial-gradient(circle at 50% 0%, rgba(6, 182, 212, 0.2), transparent 70%); padding: 4rem 0 3rem; border-bottom: 1px solid rgba(255,255,255,0.06); text-align: center;">
        <div class="container">
            <span class="badge badge-accent" style="margin-bottom: 0.75rem;"><i class="fa-solid fa-magnifying-glass-location"></i> Canlı İzləmə</span>
            <h1 class="font-heading" style="font-size: 2.8rem; margin-bottom: 0.75rem;">Qəbul Müraciətinin Statusu</h1>
            <p style="color: #94a3b8; font-size: 1.1rem; max-width: 650px; margin: 0 auto;">
                Sizə təqdim olunmuş ərizə nömrəsini (məsələn: APP-2026-9812) daxil edərək qəbul komissiyasının rəyini canlı izləyin.
            </p>
        </div>
    </div>

    <div class="container" style="padding: 3.5rem 1.5rem; max-width: 850px;">
        
        <!-- Search Code Form -->
        <div class="glass-panel" style="padding: 1.5rem; margin-bottom: 2.5rem;">
            <form action="{{ route('admissions.track') }}" method="GET" style="display: flex; gap: 1rem; align-items: center;">
                <input type="text" name="app_number" value="{{ $appNumber }}" placeholder="Müraciət nömrənizi daxil edin (Məs: APP-2026-9812)..." class="form-input font-mono" style="font-size: 1.05rem;" required>
                <button type="submit" class="btn gradient-btn" style="white-space: nowrap; padding: 0.8rem 1.75rem;">
                    <i class="fa-solid fa-magnifying-glass"></i> Yoxla
                </button>
            </form>
        </div>

        @if($appNumber && !$application)
            <div class="glass-panel" style="padding: 3rem; text-align: center; border-color: rgba(239, 68, 68, 0.3);">
                <i class="fa-solid fa-circle-xmark" style="font-size: 3rem; color: #f87171; margin-bottom: 1rem;"></i>
                <h3 class="font-heading" style="font-size: 1.4rem; margin-bottom: 0.5rem;">Müraciət tapılmadı</h3>
                <p style="color: #94a3b8;">Daxil etdiyiniz <strong>{{ $appNumber }}</strong> nömrəsi üzrə heç bir ərizə qeydə alınmayıb. Zəhmət olmasa nömrəni düzgün daxil etdiyinizdən əmin olun.</p>
            </div>
        @elseif($application)
            
            <div class="glass-panel" style="padding: 2.5rem; border-color: rgba(99, 102, 241, 0.4);">
                
                <!-- Status Timeline Nodes -->
                <div style="margin-bottom: 2.5rem;">
                    <div style="font-size: 0.85rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; margin-bottom: 1.5rem;">QƏBUL MƏRHƏLƏLƏRİ</div>
                    
                    @php
                        $statuses = [
                            'submitted' => ['title' => 'Ərizə Daxil Oldu', 'icon' => 'fa-file-arrow-up'],
                            'under_review' => ['title' => 'Komissiya Yoxlayır', 'icon' => 'fa-user-check'],
                            'interview_scheduled' => ['title' => 'Müsahibə Təyin Edildi', 'icon' => 'fa-comments'],
                            'accepted' => ['title' => 'Tələbəliyə Qəbul Edildi!', 'icon' => 'fa-award'],
                        ];
                        $statusOrder = ['submitted', 'under_review', 'interview_scheduled', 'accepted'];
                        $currentIndex = array_search($application->status, $statusOrder);
                        if ($currentIndex === false && $application->status === 'rejected') {
                            $currentIndex = 1;
                        }
                    @endphp

                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; position: relative;">
                        @foreach($statuses as $key => $st)
                            @php
                                $idx = array_search($key, $statusOrder);
                                $isCompleted = $idx <= $currentIndex && $application->status !== 'rejected';
                                $isCurrent = $key === $application->status;
                            @endphp
                            <div style="text-align: center; position: relative; z-index: 2;">
                                <div style="width: 46px; height: 46px; margin: 0 auto 0.75rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;
                                    background: {{ $isCompleted ? 'linear-gradient(135deg, #10b981, #059669)' : ($isCurrent ? '#6366f1' : 'rgba(255,255,255,0.05)') }};
                                    border: 2px solid {{ $isCompleted ? '#34d399' : ($isCurrent ? '#818cf8' : 'rgba(255,255,255,0.15)') }};
                                    color: {{ $isCompleted || $isCurrent ? '#fff' : '#64748b' }};">
                                    <i class="fa-solid {{ $st['icon'] }}"></i>
                                </div>
                                <div style="font-size: 0.85rem; font-weight: {{ $isCurrent ? '700' : '500' }}; color: {{ $isCurrent ? '#fff' : ($isCompleted ? '#34d399' : '#64748b') }};">
                                    {{ $st['title'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Application Data Details -->
                <div style="background: rgba(0,0,0,0.25); border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1.5rem; margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid rgba(255,255,255,0.06);">
                        <div>
                            <span style="font-size: 0.8rem; color: #94a3b8;">Müraciət Nömrəsi:</span>
                            <span class="font-mono" style="font-weight: 700; color: #818cf8; font-size: 1.1rem; margin-left: 0.35rem;">{{ $application->application_number }}</span>
                        </div>
                        <div>
                            @if($application->status === 'accepted')
                                <span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> Qəbul Təsdiqləndi</span>
                            @elseif($application->status === 'rejected')
                                <span class="badge badge-danger"><i class="fa-solid fa-circle-xmark"></i> İmtina Edildi</span>
                            @else
                                <span class="badge badge-primary"><i class="fa-solid fa-spinner fa-spin"></i> İcrada</span>
                            @endif
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.9rem;">
                        <div><span style="color: #94a3b8;">Namizəd:</span> <strong style="color: #fff;">{{ $application->full_name }}</strong></div>
                        <div><span style="color: #94a3b8;">İxtisas:</span> <strong style="color: #38bdf8;">{{ $application->program->name }}</strong></div>
                        <div><span style="color: #94a3b8;">İmtahan Balı:</span> <strong style="color: #fff;">{{ $application->exam_score ?? 'Gözlənilir' }} ({{ $application->exam_type }})</strong></div>
                        <div><span style="color: #94a3b8;">Tarix:</span> <span style="color: #cbd5e1;">{{ $application->created_at->format('d.m.Y H:i') }}</span></div>
                    </div>

                    @if($application->reviewer_notes)
                        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.06); font-size: 0.9rem;">
                            <span style="color: #818cf8; font-weight: 600;"><i class="fa-solid fa-comment-dots"></i> Qəbul Komissiyasının Qeydi:</span>
                            <p style="color: #cbd5e1; margin-top: 0.35rem;">{{ $application->reviewer_notes }}</p>
                        </div>
                    @endif
                </div>

            </div>

        @endif

    </div>

@endsection
