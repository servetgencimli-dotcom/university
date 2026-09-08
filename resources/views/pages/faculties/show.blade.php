@extends('layouts.app')

@section('title', "{$faculty->name} — Xəzər Universiteti")

@section('content')

    <!-- Faculty Header -->
    <div style="background: radial-gradient(circle at 50% 0%, rgba(99, 102, 241, 0.25), transparent 70%); padding: 4.5rem 0 3.5rem; border-bottom: 1px solid rgba(255,255,255,0.08);">
        <div class="container">
            <div style="display: flex; gap: 0.5rem; font-size: 0.85rem; color: #94a3b8; margin-bottom: 1rem;">
                <a href="{{ route('home') }}" style="color: #94a3b8; text-decoration: none;">Əsas</a>
                <span>/</span>
                <a href="{{ route('faculties.index') }}" style="color: #94a3b8; text-decoration: none;">Fakültələr</a>
                <span>/</span>
                <span style="color: #818cf8;">{{ $faculty->code }}</span>
            </div>

            <div style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 1.25rem;">
                <div style="width: 70px; height: 70px; background: rgba(99, 102, 241, 0.2); border: 1px solid rgba(99, 102, 241, 0.4); border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: #818cf8;">
                    <i class="fa-solid fa-{{ $faculty->icon ?? 'building-columns' }}"></i>
                </div>
                <div>
                    <span class="font-mono badge badge-primary" style="margin-bottom: 0.35rem;">{{ $faculty->code }}</span>
                    <h1 class="font-heading" style="font-size: 2.6rem; line-height: 1.2;">{{ $faculty->name }}</h1>
                </div>
            </div>

            <p style="color: #cbd5e1; font-size: 1.1rem; line-height: 1.7; max-width: 900px; margin-bottom: 1.5rem;">
                {{ $faculty->description }}
            </p>

            <div style="display: flex; gap: 2rem; font-size: 0.95rem; color: #94a3b8;">
                <div><i class="fa-solid fa-user-tie text-indigo-400"></i> <strong>Dekan:</strong> {{ $faculty->dean_name }}</div>
                <div><i class="fa-solid fa-calendar text-indigo-400"></i> <strong>Təsis edilib:</strong> {{ $faculty->established_year }}</div>
                <div><i class="fa-solid fa-users text-indigo-400"></i> <strong>Tələbələr:</strong> {{ number_format($faculty->students_count) }}+</div>
            </div>
        </div>
    </div>

    <!-- Departments and Programs -->
    <div class="container" style="padding: 4rem 1.5rem;">
        <h2 class="font-heading" style="font-size: 2rem; margin-bottom: 2rem;">Fakültənin Kafedraları və Tədris İxtisasları</h2>

        <div style="display: flex; flex-direction: column; gap: 2.5rem;">
            @foreach($faculty->departments as $dept)
                <div class="glass-panel" style="padding: 2rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.06); padding-bottom: 1rem;">
                        <div>
                            <span class="font-mono badge badge-accent" style="margin-bottom: 0.35rem;">{{ $dept->code }}</span>
                            <h3 class="font-heading" style="font-size: 1.4rem; color: #fff;">{{ $dept->name }}</h3>
                            <div style="font-size: 0.85rem; color: #94a3b8; margin-top: 0.2rem;">Kafedra Müdiri: <strong style="color: #cbd5e1;">{{ $dept->head_name }}</strong></div>
                        </div>
                        <span class="badge badge-primary">{{ $dept->programs->count() }} İxtisas</span>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; margin-top: 1.25rem;">
                        @foreach($dept->programs as $prog)
                            <div class="glass-card" style="padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between;">
                                <div>
                                    <span class="badge badge-accent" style="margin-bottom: 0.5rem;">{{ strtoupper($prog->degree_level) }}</span>
                                    <h4 class="font-heading" style="font-size: 1.1rem; margin-bottom: 0.5rem;">
                                        <a href="{{ route('programs.show', $prog->slug) }}" style="color: #fff; text-decoration: none;">
                                            {{ $prog->name }}
                                        </a>
                                    </h4>
                                    <div style="font-size: 0.8rem; color: #94a3b8; margin-bottom: 1rem;">
                                        {{ $prog->duration_years }} il • {{ $prog->credits_total }} Kredit • {{ $prog->language }}
                                    </div>
                                </div>
                                <a href="{{ route('programs.show', $prog->slug) }}" class="btn btn-outline" style="padding: 0.45rem; font-size: 0.8rem;">
                                    Təfərrüatlara Bax <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

@endsection
