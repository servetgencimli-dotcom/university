@extends('layouts.app')

@section('title', 'Xəzər Universiteti — Gələcəyin Təhsili və Tədqiqatı (Təsis: 1991)')

@section('content')

    <!-- HERO SECTION -->
    <section style="position: relative; padding: 6rem 0 5rem; overflow: hidden; background: radial-gradient(circle at 50% 20%, rgba(0, 163, 224, 0.18) 0%, rgba(0, 43, 73, 0.1) 45%, transparent 70%);">
        
        <!-- Background Glow -->
        <div style="position: absolute; top: -100px; left: 50%; transform: translateX(-50%); width: 750px; height: 350px; background: radial-gradient(circle, rgba(0, 163, 224, 0.25), transparent 70%); filter: blur(60px); pointer-events: none;"></div>

        <div class="container" style="position: relative; z-index: 10; text-align: center;">
            
            <!-- Pill Badge -->
            <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(0, 163, 224, 0.12); border: 1px solid rgba(0, 163, 224, 0.3); padding: 0.4rem 1rem; border-radius: 9999px; margin-bottom: 1.75rem;">
                <span style="width: 8px; height: 8px; background: #C89D35; border-radius: 50%; box-shadow: 0 0 10px #C89D35;"></span>
                <span style="font-size: 0.85rem; font-weight: 600; color: #cbd5e1;">Azərbaycanda İlk Amerika Kredit Sistemli Ali Məktəb (1991)</span>
            </div>

            <!-- Main Heading -->
            <h1 class="font-heading" style="font-size: 3.6rem; line-height: 1.15; max-width: 950px; margin: 0 auto 1.5rem; letter-spacing: -0.03em;">
                Bilik, İnnovasiya və <span class="gradient-text">Qlobal Liderlik</span>
            </h1>

            <p style="font-size: 1.2rem; color: #94a3b8; max-width: 740px; margin: 0 auto 2.5rem; line-height: 1.7;">
                Xəzər Universiteti — Təbiət elmləri, mühəndislik, iqtisadiyyat, humanitar elmlər və baytarlıq təbabəti sahəsində beynəlxalq səviyyəli tədris və tədqiqat mərkəzidir.
            </p>

            <!-- Hero Action Buttons -->
            <div style="display: flex; align-items: center; justify-content: center; gap: 1rem; margin-bottom: 4rem;">
                <a href="{{ route('programs.index') }}" class="btn gradient-btn" style="padding: 0.9rem 2rem; font-size: 1.05rem;">
                    <i class="fa-solid fa-compass"></i> İxtisasları Kəşf Et
                </a>
                <a href="{{ route('admissions.apply') }}" class="btn btn-outline" style="padding: 0.9rem 2rem; font-size: 1.05rem; border-color: rgba(255,255,255,0.25);">
                    <i class="fa-solid fa-paper-plane"></i> Onlayn Müraciət Et
                </a>
                <a href="{{ route('campus.map') }}" class="btn btn-outline" style="padding: 0.9rem 1.5rem; font-size: 1.05rem; color: #38bdf8; border-color: rgba(56, 189, 248, 0.3);">
                    <i class="fa-solid fa-map-location-dot"></i> Kampuslarımız
                </a>
            </div>

            <!-- SMART PROGRAM FINDER SEARCH WIDGET -->
            <div class="glass-panel" style="max-width: 1050px; margin: 0 auto; padding: 1.5rem; border-color: rgba(0, 163, 224, 0.35); box-shadow: 0 20px 50px -10px rgba(0,0,0,0.5);">
                <form action="{{ route('programs.index') }}" method="GET" style="display: grid; grid-template-columns: 2fr 1.5fr 1.5fr auto; gap: 1rem; align-items: center;">
                    
                    <div>
                        <div style="font-size: 0.75rem; text-align: left; color: #94a3b8; margin-bottom: 0.35rem; font-weight: 600;">İXTİSAS VƏ YA AÇAR SÖZ</div>
                        <input type="text" name="search" placeholder="Məs: Computer Science, Petroleum, Finance..." class="form-input">
                    </div>

                    <div>
                        <div style="font-size: 0.75rem; text-align: left; color: #94a3b8; margin-bottom: 0.35rem; font-weight: 600;">TƏHSİL PİLLƏSİ</div>
                        <select name="degree" class="form-select">
                            <option value="">Bütün Pillələr</option>
                            <option value="bachelor">Bakalavr (BSc / BA)</option>
                            <option value="master">Magistratura (MSc / MBA)</option>
                            <option value="phd">Doktorantura (PhD)</option>
                        </select>
                    </div>

                    <div>
                        <div style="font-size: 0.75rem; text-align: left; color: #94a3b8; margin-bottom: 0.35rem; font-weight: 600;">FAKÜLTƏ / MƏKTƏB</div>
                        <select name="faculty_id" class="form-select">
                            <option value="">Bütün Məktəblər</option>
                            @foreach($faculties as $fac)
                                <option value="{{ $fac->id }}">{{ $fac->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="align-self: flex-end;">
                        <button type="submit" class="btn gradient-btn" style="height: 44px; padding: 0 1.75rem; width: 100%;">
                            <i class="fa-solid fa-magnifying-glass"></i> Tap
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </section>

    <!-- KEY STATS COUNTER STRIP -->
    <section style="background: rgba(8, 18, 36, 0.75); border-top: 1px solid rgba(255,255,255,0.06); border-bottom: 1px solid rgba(255,255,255,0.06); padding: 2.5rem 0;">
        <div class="container">
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 2rem; text-align: center;">
                
                <div>
                    <div class="font-heading gradient-text" style="font-size: 2.75rem; line-height: 1;">5,500+</div>
                    <div style="color: #94a3b8; font-size: 0.95rem; margin-top: 0.4rem; font-weight: 500;">Tələbə və Magistrant</div>
                </div>

                <div>
                    <div class="font-heading gradient-gold" style="font-size: 2.75rem; line-height: 1;">96.5%</div>
                    <div style="color: #94a3b8; font-size: 0.95rem; margin-top: 0.4rem; font-weight: 500;">Məzunların Məşğulluq Göstəricisi</div>
                </div>

                <div>
                    <div class="font-heading" style="font-size: 2.75rem; line-height: 1; color: #38bdf8;">300+</div>
                    <div style="color: #94a3b8; font-size: 0.95rem; margin-top: 0.4rem; font-weight: 500;">Professor-Müəllim Heyəti</div>
                </div>

                <div>
                    <div class="font-heading" style="font-size: 2.75rem; line-height: 1; color: #C89D35;">Top 1 (Azərbaycan)</div>
                    <div style="color: #94a3b8; font-size: 0.95rem; margin-top: 0.4rem; font-weight: 500;">QS & THE Sitat Reytinqi</div>
                </div>

            </div>
        </div>
    </section>

    <!-- POPULAR PROGRAMS GRID -->
    <section style="padding: 5rem 0;">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 3rem;">
                <div>
                    <span class="badge badge-primary" style="margin-bottom: 0.75rem;"><i class="fa-solid fa-star"></i> Seçilən Proqramlar</span>
                    <h2 class="font-heading" style="font-size: 2.3rem;">Aparıcı Tədris İxtisaslarımız</h2>
                </div>
                <a href="{{ route('programs.index') }}" class="btn btn-outline">
                    Bütün İxtisaslar <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.75rem;">
                @foreach($popularPrograms as $prog)
                    <div class="glass-card" style="padding: 1.75rem; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                <span class="badge badge-accent">{{ strtoupper($prog->degree_level) }}</span>
                                <span class="font-mono" style="font-size: 0.8rem; color: #94a3b8; background: rgba(255,255,255,0.05); padding: 0.2rem 0.5rem; border-radius: 4px;">{{ $prog->code }}</span>
                            </div>

                            <h3 class="font-heading" style="font-size: 1.3rem; margin-bottom: 0.75rem; line-height: 1.3;">
                                <a href="{{ route('programs.show', $prog->slug) }}" style="color: #fff; text-decoration: none;">
                                    {{ $prog->name }}
                                </a>
                            </h3>

                            <p style="color: #94a3b8; font-size: 0.9rem; line-height: 1.6; margin-bottom: 1.25rem;">
                                {{ Str::limit($prog->overview, 110) }}
                            </p>

                            <div style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.85rem; color: #cbd5e1; margin-bottom: 1.5rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.06);">
                                <div style="display: flex; justify-content: space-between;">
                                    <span style="color: #94a3b8;"><i class="fa-solid fa-clock"></i> Müddət & Kredit:</span>
                                    <span style="font-weight: 600;">{{ $prog->duration_years }} il ({{ $prog->credits_total }} ECTS)</span>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span style="color: #94a3b8;"><i class="fa-solid fa-language"></i> Tədris Dili:</span>
                                    <span style="font-weight: 600;">{{ $prog->language }}</span>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span style="color: #94a3b8;"><i class="fa-solid fa-coins"></i> İllik Təhsil Haqqı:</span>
                                    <span style="font-weight: 700; color: #38bdf8;">{{ number_format($prog->tuition_fee_azn) }} ₼ / il</span>
                                </div>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                            <a href="{{ route('programs.show', $prog->slug) }}" class="btn btn-outline" style="padding: 0.55rem; font-size: 0.85rem;">
                                Təfərrüat
                            </a>
                            <a href="{{ route('admissions.apply', ['program_id' => $prog->id]) }}" class="btn gradient-btn" style="padding: 0.55rem; font-size: 0.85rem;">
                                Müraciət Et
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- SCHOOLS SHOWCASE -->
    <section style="padding: 5rem 0; background: rgba(8, 18, 36, 0.6); border-top: 1px solid rgba(255,255,255,0.06);">
        <div class="container">
            <div style="text-align: center; max-width: 700px; margin: 0 auto 3.5rem;">
                <span class="badge badge-accent" style="margin-bottom: 0.75rem;"><i class="fa-solid fa-building-columns"></i> Məktəblər</span>
                <h2 class="font-heading" style="font-size: 2.3rem; margin-bottom: 1rem;">Akademik Məktəblərimiz</h2>
                <p style="color: #94a3b8; font-size: 1rem;">
                    Xəzər Universitetinin 4 əsas məktəbi müasir tədris proqramları və beynəlxalq ikili diplom imkanları təklif edir.
                </p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem;">
                @foreach($faculties as $faculty)
                    <div class="glass-card" style="padding: 2.25rem; display: flex; gap: 1.5rem; align-items: flex-start;">
                        <div style="width: 55px; height: 55px; background: rgba(0, 163, 224, 0.15); border: 1px solid rgba(0, 163, 224, 0.3); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #38bdf8; flex-shrink: 0;">
                            <i class="fa-solid fa-{{ $faculty->icon ?? 'graduation-cap' }}"></i>
                        </div>

                        <div>
                            <div style="font-size: 0.8rem; color: #C89D35; font-weight: 700; text-transform: uppercase; margin-bottom: 0.25rem;">
                                {{ $faculty->code }} • Təsis: {{ $faculty->established_year }}
                            </div>

                            <h3 class="font-heading" style="font-size: 1.35rem; margin-bottom: 0.5rem; line-height: 1.3;">
                                <a href="{{ route('faculties.show', $faculty->slug) }}" style="color: #fff; text-decoration: none;">
                                    {{ $faculty->name }}
                                </a>
                            </h3>

                            <div style="font-size: 0.85rem; color: #38bdf8; font-weight: 600; margin-bottom: 0.75rem;">
                                Dekan: {{ $faculty->dean_name }}
                            </div>

                            <p style="color: #94a3b8; font-size: 0.9rem; line-height: 1.6; margin-bottom: 1.25rem;">
                                {{ Str::limit($faculty->description, 130) }}
                            </p>

                            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.06); font-size: 0.85rem;">
                                <span style="color: #cbd5e1;"><strong>{{ $faculty->departments->count() }}</strong> Departament</span>
                                <a href="{{ route('faculties.show', $faculty->slug) }}" style="color: #38bdf8; text-decoration: none; font-weight: 600;">
                                    Ətraflı Bax <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- RESEARCH HIGHLIGHTS & EVENTS -->
    <section style="padding: 5rem 0;">
        <div class="container">
            <div style="display: grid; grid-template-columns: 1.3fr 1fr; gap: 3.5rem;">
                
                <!-- Research Section -->
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                        <div>
                            <span class="badge badge-primary" style="margin-bottom: 0.5rem;"><i class="fa-solid fa-atom"></i> Elm & İxtira</span>
                            <h2 class="font-heading" style="font-size: 1.9rem;">Son Elmi Nəşrlər</h2>
                        </div>
                        <a href="{{ route('research.index') }}" style="color: #38bdf8; text-decoration: none; font-size: 0.9rem; font-weight: 600;">
                            Bütün Məqalələr <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                        @foreach($featuredResearch as $paper)
                            <div class="glass-card" style="padding: 1.5rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <span class="badge badge-accent">{{ $paper->category }}</span>
                                    <span style="font-size: 0.8rem; color: #94a3b8;"><i class="fa-solid fa-quote-right"></i> {{ $paper->citations_count }} Sitat</span>
                                </div>
                                <h4 class="font-heading" style="font-size: 1.15rem; margin-bottom: 0.5rem; color: #fff;">
                                    {{ $paper->title }}
                                </h4>
                                <div style="font-size: 0.85rem; color: #38bdf8; margin-bottom: 0.75rem;">
                                    Müəllif: {{ $paper->author?->name ?? 'Professor' }} ({{ $paper->journal_name }})
                                </div>
                                <p style="color: #cbd5e1; font-size: 0.85rem; line-height: 1.6;">
                                    {{ Str::limit($paper->abstract, 140) }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Upcoming Events Section -->
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                        <div>
                            <span class="badge badge-accent" style="margin-bottom: 0.5rem;"><i class="fa-solid fa-calendar"></i> Təqvim</span>
                            <h2 class="font-heading" style="font-size: 1.9rem;">Yaxınlaşan Tədbirlər</h2>
                        </div>
                        <a href="{{ route('events.index') }}" style="color: #38bdf8; text-decoration: none; font-size: 0.9rem; font-weight: 600;">
                            Hamısı <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                        @foreach($featuredEvents as $event)
                            <div class="glass-card" style="padding: 1.25rem; display: flex; gap: 1.25rem; align-items: center;">
                                <div style="background: rgba(0, 163, 224, 0.15); border: 1px solid rgba(0, 163, 224, 0.3); border-radius: 0.75rem; padding: 0.75rem 1rem; text-align: center; min-width: 70px;">
                                    <div class="font-heading" style="font-size: 1.4rem; line-height: 1; color: #38bdf8;">{{ $event->start_time->format('d') }}</div>
                                    <div style="font-size: 0.75rem; text-transform: uppercase; color: #cbd5e1; font-weight: 700;">{{ $event->start_time->translatedFormat('M') }}</div>
                                </div>
                                <div>
                                    <h4 class="font-heading" style="font-size: 1.05rem; margin-bottom: 0.3rem;">
                                        <a href="{{ route('events.show', $event->slug) }}" style="color: #fff; text-decoration: none;">
                                            {{ $event->title }}
                                        </a>
                                    </h4>
                                    <div style="font-size: 0.8rem; color: #94a3b8;">
                                        <i class="fa-solid fa-location-dot"></i> {{ $event->venue }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection
