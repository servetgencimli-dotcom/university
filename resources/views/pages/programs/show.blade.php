@extends('layouts.app')

@section('title', "{$program->name} — Xəzər Universiteti")

@section('content')

    <!-- Program Hero Banner -->
    <div style="background: radial-gradient(circle at 50% 0%, rgba(99, 102, 241, 0.25), transparent 70%); padding: 4.5rem 0 3.5rem; border-bottom: 1px solid rgba(255,255,255,0.08);">
        <div class="container">
            <div style="display: flex; gap: 0.5rem; font-size: 0.85rem; color: #94a3b8; margin-bottom: 1rem;">
                <a href="{{ route('home') }}" style="color: #94a3b8; text-decoration: none;">Əsas</a>
                <span>/</span>
                <a href="{{ route('programs.index') }}" style="color: #94a3b8; text-decoration: none;">İxtisaslar</a>
                <span>/</span>
                <span style="color: #818cf8;">{{ $program->code }}</span>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 3rem; align-items: center;">
                <div>
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                        <span class="badge badge-accent">{{ strtoupper($program->degree_level) }} DƏRƏCƏSİ</span>
                        <span class="font-mono badge badge-primary">{{ $program->code }}</span>
                    </div>

                    <h1 class="font-heading" style="font-size: 2.8rem; line-height: 1.2; margin-bottom: 1rem;">
                        {{ $program->name }}
                    </h1>

                    <div style="font-size: 1rem; color: #38bdf8; font-weight: 600; margin-bottom: 1.5rem;">
                        <i class="fa-solid fa-building-columns"></i> {{ $program->department->faculty->name }} • {{ $program->department->name }}
                    </div>

                    <p style="color: #cbd5e1; font-size: 1.05rem; line-height: 1.7; max-width: 800px;">
                        {{ $program->overview }}
                    </p>
                </div>

                <!-- Quick Apply Card -->
                <div class="glass-panel" style="padding: 2rem; border-color: rgba(99, 102, 241, 0.4);">
                    <div style="font-size: 0.85rem; color: #94a3b8; margin-bottom: 0.25rem;">İllik Təhsil Haqqı</div>
                    <div class="font-heading" style="font-size: 2.2rem; color: #38bdf8; margin-bottom: 1.25rem;">
                        {{ number_format($program->tuition_fee_azn) }} ₼ <span style="font-size: 1rem; color: #94a3b8;">/ il</span>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.9rem; color: #cbd5e1; margin-bottom: 1.75rem;">
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.06); padding-bottom: 0.5rem;">
                            <span style="color: #94a3b8;">Tədris Müddəti:</span>
                            <span style="font-weight: 600;">{{ $program->duration_years }} İl</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.06); padding-bottom: 0.5rem;">
                            <span style="color: #94a3b8;">Ümumi Kreditlər:</span>
                            <span style="font-weight: 600;">{{ $program->credits_total }} ECTS</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.06); padding-bottom: 0.5rem;">
                            <span style="color: #94a3b8;">Tədris Dili:</span>
                            <span style="font-weight: 600;">{{ $program->language }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #94a3b8;">Təhsil Forması:</span>
                            <span style="font-weight: 600;">{{ $program->study_mode }}</span>
                        </div>
                    </div>

                    <a href="{{ route('admissions.apply', ['program_id' => $program->id]) }}" class="btn gradient-btn" style="width: 100%; padding: 0.85rem; font-size: 1rem;">
                        <i class="fa-solid fa-paper-plane"></i> Bu İxtisasa Müraciət Et
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Sections -->
    <div class="container" style="padding: 4rem 1.5rem;">
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 3.5rem;">
            
            <!-- Left Main Column -->
            <div>
                
                <!-- Career Opportunities -->
                <div style="margin-bottom: 3.5rem;">
                    <h2 class="font-heading" style="font-size: 1.75rem; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.65rem;">
                        <i class="fa-solid fa-briefcase text-indigo-400"></i> Karyera və İş İmkanları
                    </h2>
                    <div class="glass-card" style="padding: 1.75rem;">
                        <p style="color: #cbd5e1; font-size: 1rem; line-height: 1.7;">
                            {{ $program->career_opportunities }}
                        </p>
                    </div>
                </div>

                <!-- Admission Requirements -->
                <div style="margin-bottom: 3.5rem;">
                    <h2 class="font-heading" style="font-size: 1.75rem; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.65rem;">
                        <i class="fa-solid fa-clipboard-check text-cyan-400"></i> Qəbul Tələbləri & Keçid Balları
                    </h2>
                    <div class="glass-card" style="padding: 1.75rem;">
                        <p style="color: #cbd5e1; font-size: 1rem; line-height: 1.7; margin-bottom: 1.25rem;">
                            {{ $program->admission_requirements }}
                        </p>
                        <div class="badge badge-primary" style="font-size: 0.85rem; padding: 0.4rem 0.85rem;">
                            <i class="fa-solid fa-chart-simple"></i> Minimum Müsabiqə Balı: {{ $program->min_score_required }}
                        </div>
                    </div>
                </div>

                <!-- Curriculum & Courses -->
                <div>
                    <h2 class="font-heading" style="font-size: 1.75rem; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.65rem;">
                        <i class="fa-solid fa-book-bookmark text-emerald-400"></i> Tədris Planı və Fənlər (Sillabus)
                    </h2>

                    @if($program->courses->isEmpty())
                        <div class="glass-card" style="padding: 2rem; color: #94a3b8; text-align: center;">
                            Cari semestr üçün tədris planı tezliklə yayımlanacaq.
                        </div>
                    @else
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            @foreach($program->courses as $course)
                                <div class="glass-card" style="padding: 1.5rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <span class="font-mono badge badge-accent">{{ $course->code }}</span>
                                            <h4 class="font-heading" style="font-size: 1.15rem; color: #fff;">{{ $course->name }}</h4>
                                        </div>
                                        <span class="badge {{ $course->type === 'compulsory' ? 'badge-primary' : 'badge-warning' }}">
                                            {{ $course->type === 'compulsory' ? 'Məcburi Fənn' : 'Seçmə Fənn' }}
                                        </span>
                                    </div>
                                    <div style="display: flex; gap: 1.5rem; font-size: 0.85rem; color: #94a3b8; margin-bottom: 0.75rem;">
                                        <span><i class="fa-solid fa-award"></i> {{ $course->credits }} ECTS Kredit</span>
                                        <span><i class="fa-solid fa-calendar-week"></i> {{ $course->semester }}-ci Semestr</span>
                                    </div>
                                    @if($course->syllabus)
                                        <p style="color: #cbd5e1; font-size: 0.9rem; line-height: 1.6; background: rgba(0,0,0,0.2); padding: 0.75rem; border-radius: 0.5rem;">
                                            <strong>Sillabus qısa xülasəsi:</strong> {{ $course->syllabus }}
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

            <!-- Right Sidebar: Related Programs -->
            <div>
                <h3 class="font-heading" style="font-size: 1.3rem; margin-bottom: 1.25rem; color: #fff;">
                    Oxşar İxtisaslar
                </h3>

                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    @foreach($relatedPrograms as $rel)
                        <div class="glass-card" style="padding: 1.25rem;">
                            <span class="badge badge-accent" style="margin-bottom: 0.4rem;">{{ strtoupper($rel->degree_level) }}</span>
                            <h4 class="font-heading" style="font-size: 1.05rem; margin-bottom: 0.5rem;">
                                <a href="{{ route('programs.show', $rel->slug) }}" style="color: #fff; text-decoration: none;">
                                    {{ $rel->name }}
                                </a>
                            </h4>
                            <div style="font-size: 0.85rem; color: #38bdf8; font-weight: 700;">
                                {{ number_format($rel->tuition_fee_azn) }} ₼ / il
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

@endsection
