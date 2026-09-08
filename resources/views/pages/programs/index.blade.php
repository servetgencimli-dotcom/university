@extends('layouts.app')

@section('title', 'Akademik İxtisaslar və Proqramlar — Xəzər Universiteti')

@section('content')

    <!-- Header Banner -->
    <div style="background: radial-gradient(circle at 50% 0%, rgba(99, 102, 241, 0.2), transparent 70%); padding: 4rem 0 3rem; border-bottom: 1px solid rgba(255,255,255,0.06); text-align: center;">
        <div class="container">
            <span class="badge badge-accent" style="margin-bottom: 0.75rem;"><i class="fa-solid fa-graduation-cap"></i> Akademik Kataloq</span>
            <h1 class="font-heading" style="font-size: 2.8rem; margin-bottom: 0.75rem;">Akademik İxtisaslar və Tədris Proqramları</h1>
            <p style="color: #94a3b8; font-size: 1.1rem; max-width: 650px; margin: 0 auto;">
                Bakalavriat, Magistratura və Doktorantura pillələri üzrə beynəlxalq standartlara uyğun müasir ixtisaslar.
            </p>
        </div>
    </div>

    <!-- Main Content with Sidebar Filter & Grid -->
    <div class="container" style="padding: 3.5rem 1.5rem;">
        <div style="display: grid; grid-template-columns: 280px 1fr; gap: 2.5rem; align-items: start;">
            
            <!-- Filters Sidebar -->
            <div class="glass-panel" style="padding: 1.75rem; position: sticky; top: 100px;">
                <h3 class="font-heading" style="font-size: 1.15rem; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
                    <span><i class="fa-solid fa-filter text-indigo-400"></i> Süzgəc</span>
                    <a href="{{ route('programs.index') }}" style="font-size: 0.75rem; color: #94a3b8; text-decoration: none; font-weight: normal;">Sıfırla</a>
                </h3>

                <form action="{{ route('programs.index') }}" method="GET" style="display: flex; flex-direction: column; gap: 1.25rem;">
                    
                    <!-- Search Input -->
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 0.4rem;">Axtarış</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="İxtisas adı..." class="form-input">
                    </div>

                    <!-- Degree Level -->
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 0.4rem;">Təhsil Pilləsi</label>
                        <select name="degree" class="form-select">
                            <option value="">Hamısı</option>
                            <option value="bachelor" {{ request('degree') == 'bachelor' ? 'selected' : '' }}>Bakalavr (BSc)</option>
                            <option value="master" {{ request('degree') == 'master' ? 'selected' : '' }}>Magistratura (MSc)</option>
                            <option value="phd" {{ request('degree') == 'phd' ? 'selected' : '' }}>Doktorantura (PhD)</option>
                        </select>
                    </div>

                    <!-- Faculty -->
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 0.4rem;">Fakültə</label>
                        <select name="faculty_id" class="form-select">
                            <option value="">Bütün Fakültələr</option>
                            @foreach($faculties as $fac)
                                <option value="{{ $fac->id }}" {{ request('faculty_id') == $fac->id ? 'selected' : '' }}>{{ $fac->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Language -->
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 0.4rem;">Tədris Dili</label>
                        <select name="language" class="form-select">
                            <option value="">Bütün Dillər</option>
                            <option value="Azərbaycan" {{ request('language') == 'Azərbaycan' ? 'selected' : '' }}>Azərbaycan dili</option>
                            <option value="İngilis" {{ request('language') == 'İngilis' ? 'selected' : '' }}>İngilis dili</option>
                        </select>
                    </div>

                    <button type="submit" class="btn gradient-btn" style="width: 100%; margin-top: 0.5rem;">
                        <i class="fa-solid fa-sliders"></i> Filtrləri Tətbiq Et
                    </button>
                </form>
            </div>

            <!-- Programs Grid -->
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; color: #94a3b8; font-size: 0.9rem;">
                    <div>Cəmi <strong>{{ $programs->total() }}</strong> ixtisas tapıldı</div>
                </div>

                @if($programs->isEmpty())
                    <div class="glass-panel" style="padding: 3rem; text-align: center;">
                        <i class="fa-solid fa-circle-question" style="font-size: 3rem; color: #64748b; margin-bottom: 1rem;"></i>
                        <h3 class="font-heading" style="font-size: 1.3rem; margin-bottom: 0.5rem;">Axtarışa uyğun ixtisas tapılmadı</h3>
                        <p style="color: #94a3b8; margin-bottom: 1.5rem;">Zəhmət olmasa süzgəc parametrlərini dəyişdirərək yenidən yoxlayın.</p>
                        <a href="{{ route('programs.index') }}" class="btn btn-outline">Bütün İxtisasları Göstər</a>
                    </div>
                @else
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                        @foreach($programs as $prog)
                            <div class="glass-card" style="padding: 1.75rem; display: flex; flex-direction: column; justify-content: space-between;">
                                <div>
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                                        <span class="badge badge-accent">{{ strtoupper($prog->degree_level) }}</span>
                                        <span class="font-mono" style="font-size: 0.8rem; color: #94a3b8;">{{ $prog->code }}</span>
                                    </div>

                                    <h3 class="font-heading" style="font-size: 1.25rem; margin-bottom: 0.5rem; line-height: 1.3;">
                                        <a href="{{ route('programs.show', $prog->slug) }}" style="color: #fff; text-decoration: none;">
                                            {{ $prog->name }}
                                        </a>
                                    </h3>

                                    <div style="font-size: 0.85rem; color: #818cf8; margin-bottom: 1rem;">
                                        <i class="fa-solid fa-building-columns"></i> {{ $prog->department->faculty->name ?? 'Fakültə' }}
                                    </div>

                                    <p style="color: #94a3b8; font-size: 0.85rem; line-height: 1.6; margin-bottom: 1.25rem;">
                                        {{ Str::limit($prog->overview, 110) }}
                                    </p>

                                    <div style="display: flex; flex-direction: column; gap: 0.4rem; font-size: 0.8rem; color: #cbd5e1; margin-bottom: 1.25rem; padding: 0.75rem; background: rgba(0,0,0,0.2); border-radius: 0.5rem;">
                                        <div style="display: flex; justify-content: space-between;">
                                            <span style="color: #94a3b8;">Müddət:</span>
                                            <span>{{ $prog->duration_years }} il ({{ $prog->credits_total }} ECTS)</span>
                                        </div>
                                        <div style="display: flex; justify-content: space-between;">
                                            <span style="color: #94a3b8;">Tədris Dili:</span>
                                            <span>{{ $prog->language }}</span>
                                        </div>
                                        <div style="display: flex; justify-content: space-between;">
                                            <span style="color: #94a3b8;">Təhsil Haqqı:</span>
                                            <span style="font-weight: 700; color: #38bdf8;">{{ number_format($prog->tuition_fee_azn) }} ₼ / il</span>
                                        </div>
                                    </div>
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                    <a href="{{ route('programs.show', $prog->slug) }}" class="btn btn-outline" style="padding: 0.5rem; font-size: 0.85rem;">
                                        Sillabus & Detallar
                                    </a>
                                    <a href="{{ route('admissions.apply', ['program_id' => $prog->id]) }}" class="btn gradient-btn" style="padding: 0.5rem; font-size: 0.85rem;">
                                        Müraciət Et
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div style="margin-top: 2.5rem;">
                        {{ $programs->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

@endsection
