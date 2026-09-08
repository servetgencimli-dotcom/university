@extends('layouts.app')

@section('title', 'Fakültələr və Kafedralar — Xəzər Universiteti')

@section('content')

    <!-- Header Banner -->
    <div style="background: radial-gradient(circle at 50% 0%, rgba(6, 182, 212, 0.2), transparent 70%); padding: 4rem 0 3rem; border-bottom: 1px solid rgba(255,255,255,0.06); text-align: center;">
        <div class="container">
            <span class="badge badge-accent" style="margin-bottom: 0.75rem;"><i class="fa-solid fa-building-columns"></i> Elmi Mərkəzlər</span>
            <h1 class="font-heading" style="font-size: 2.8rem; margin-bottom: 0.75rem;">Fakültə və Kafedralarımız</h1>
            <p style="color: #94a3b8; font-size: 1.1rem; max-width: 650px; margin: 0 auto;">
                Beynəlxalq professor-müəllim heyəti və yüksək texnologiyalı laboratoriyalarla təchiz olunmuş 5 əsas fakültə.
            </p>
        </div>
    </div>

    <!-- Faculties List -->
    <div class="container" style="padding: 4rem 1.5rem;">
        <div style="display: flex; flex-direction: column; gap: 2.5rem;">
            @foreach($faculties as $faculty)
                <div class="glass-panel" style="padding: 2.5rem; display: grid; grid-template-columns: 80px 1.5fr 1fr; gap: 2.5rem; align-items: center;">
                    
                    <!-- Icon Box -->
                    <div style="width: 80px; height: 80px; background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.3); border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: #818cf8;">
                        <i class="fa-solid fa-{{ $faculty->icon ?? 'building-columns' }}"></i>
                    </div>

                    <!-- Middle Info -->
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                            <span class="font-mono badge badge-primary">{{ $faculty->code }}</span>
                            <span style="font-size: 0.85rem; color: #38bdf8; font-weight: 600;">Təsis ili: {{ $faculty->established_year }}</span>
                        </div>

                        <h2 class="font-heading" style="font-size: 1.7rem; margin-bottom: 0.5rem;">
                            <a href="{{ route('faculties.show', $faculty->slug) }}" style="color: #fff; text-decoration: none;">
                                {{ $faculty->name }}
                            </a>
                        </h2>

                        <div style="font-size: 0.9rem; color: #cbd5e1; margin-bottom: 1rem;">
                            <strong>Dekan:</strong> {{ $faculty->dean_name }}
                        </div>

                        <p style="color: #94a3b8; font-size: 0.95rem; line-height: 1.6;">
                            {{ $faculty->description }}
                        </p>
                    </div>

                    <!-- Right Stats & Action -->
                    <div style="background: rgba(0,0,0,0.25); border: 1px solid rgba(255,255,255,0.06); border-radius: 0.85rem; padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                        <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                            <span style="color: #94a3b8;"><i class="fa-solid fa-users"></i> Tələbə Sayı:</span>
                            <span style="font-weight: 700; color: #fff;">{{ number_format($faculty->students_count) }}+</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                            <span style="color: #94a3b8;"><i class="fa-solid fa-chalkboard-user"></i> Professorlar:</span>
                            <span style="font-weight: 700; color: #fff;">{{ $faculty->professors_count }}+</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                            <span style="color: #94a3b8;"><i class="fa-solid fa-layer-group"></i> Kafedralar:</span>
                            <span style="font-weight: 700; color: #fff;">{{ $faculty->departments->count() }} Bölmə</span>
                        </div>

                        <a href="{{ route('faculties.show', $faculty->slug) }}" class="btn gradient-btn" style="width: 100%; margin-top: 0.5rem; padding: 0.65rem;">
                            Fakültəyə Daxil Ol <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>

                </div>
            @endforeach
        </div>
    </div>

@endsection
