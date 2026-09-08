@extends('layouts.app')

@section('title', 'İnteraktiv Rəqəmsal Kampus Xəritəsi — Xəzər Universiteti')

@section('content')

    <!-- Header Banner -->
    <div style="background: radial-gradient(circle at 50% 0%, rgba(6, 182, 212, 0.25), transparent 70%); padding: 4rem 0 2.5rem; border-bottom: 1px solid rgba(255,255,255,0.06); text-align: center;">
        <div class="container">
            <span class="badge badge-accent" style="margin-bottom: 0.75rem;"><i class="fa-solid fa-map-location-dot"></i> Ağıllı Kampus</span>
            <h1 class="font-heading" style="font-size: 2.8rem; margin-bottom: 0.75rem;">İnteraktiv Virtual Kampus Xəritəsi</h1>
            <p style="color: #94a3b8; font-size: 1.1rem; max-width: 700px; margin: 0 auto;">
                Universitetin 50 hektarlıq innovativ şəhərciyində korpuslar, laboratoriyalar, kitabxana və yataqxanaların interaktiv vizual naviqasiyası.
            </p>
        </div>
    </div>

    <div class="container" style="padding: 3rem 1.5rem;">
        
        <!-- Category Filter Pills -->
        <div style="display: flex; gap: 0.75rem; justify-content: center; margin-bottom: 2.5rem; flex-wrap: wrap;">
            <a href="{{ route('campus.map') }}" class="btn {{ !$category || $category === 'all' ? 'gradient-btn' : 'btn-outline' }}" style="padding: 0.5rem 1.25rem; font-size: 0.9rem;">
                <i class="fa-solid fa-border-all"></i> Bütün Obyektlər ({{ $allBuildings->count() }})
            </a>
            <a href="{{ route('campus.map', ['category' => 'academic']) }}" class="btn {{ $category === 'academic' ? 'gradient-btn' : 'btn-outline' }}" style="padding: 0.5rem 1.25rem; font-size: 0.9rem;">
                <i class="fa-solid fa-graduation-cap"></i> Tədris Korpusları
            </a>
            <a href="{{ route('campus.map', ['category' => 'research_lab']) }}" class="btn {{ $category === 'research_lab' ? 'gradient-btn' : 'btn-outline' }}" style="padding: 0.5rem 1.25rem; font-size: 0.9rem;">
                <i class="fa-solid fa-microchip"></i> Elmi Laboratoriyalar
            </a>
            <a href="{{ route('campus.map', ['category' => 'library']) }}" class="btn {{ $category === 'library' ? 'gradient-btn' : 'btn-outline' }}" style="padding: 0.5rem 1.25rem; font-size: 0.9rem;">
                <i class="fa-solid fa-book"></i> Rəqəmsal Kitabxana
            </a>
            <a href="{{ route('campus.map', ['category' => 'dormitory']) }}" class="btn {{ $category === 'dormitory' ? 'gradient-btn' : 'btn-outline' }}" style="padding: 0.5rem 1.25rem; font-size: 0.9rem;">
                <i class="fa-solid fa-hotel"></i> Tələbə Rezidensiyası
            </a>
            <a href="{{ route('campus.map', ['category' => 'sports']) }}" class="btn {{ $category === 'sports' ? 'gradient-btn' : 'btn-outline' }}" style="padding: 0.5rem 1.25rem; font-size: 0.9rem;">
                <i class="fa-solid fa-volleyball"></i> İdman Arenası
            </a>
        </div>

        <!-- Visual 2D/3D Style Interactive Canvas Map -->
        <div class="glass-panel" style="position: relative; height: 500px; border-color: rgba(99, 102, 241, 0.4); overflow: hidden; margin-bottom: 3.5rem; background: radial-gradient(circle at 50% 50%, #172554 0%, #0b0f19 80%);">
            
            <!-- Map Grid Overlay Lines -->
            <div style="position: absolute; inset: 0; background-image: linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px); background-size: 40px 40px; pointer-events: none;"></div>

            <!-- Central Campus Square Indicator -->
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); border: 2px dashed rgba(6, 182, 212, 0.3); border-radius: 50%; width: 220px; height: 220px; display: flex; align-items: center; justify-content: center; color: rgba(6, 182, 212, 0.5); font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; pointer-events: none;">
                Mərkəzi Meydan
            </div>

            <!-- Dynamic Building Nodes -->
            @foreach($buildings as $b)
                <div style="position: absolute; left: {{ $b->map_x }}%; top: {{ $b->map_y }}%; transform: translate(-50%, -50%); cursor: pointer; transition: all 0.3s ease;"
                     onmouseover="this.querySelector('.building-card-popup').style.display='block'; this.style.zIndex='20';"
                     onmouseout="this.querySelector('.building-card-popup').style.display='none'; this.style.zIndex='5';">
                    
                    <!-- Building Pin Pinpoint -->
                    <div style="display: flex; flex-direction: column; align-items: center; gap: 0.35rem;">
                        <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #6366f1, #06b6d4); border: 2px solid #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: #fff; box-shadow: 0 0 20px rgba(99, 102, 241, 0.7);">
                            @if($b->category === 'academic') <i class="fa-solid fa-graduation-cap"></i>
                            @elseif($b->category === 'research_lab') <i class="fa-solid fa-microchip"></i>
                            @elseif($b->category === 'library') <i class="fa-solid fa-book"></i>
                            @elseif($b->category === 'dormitory') <i class="fa-solid fa-bed"></i>
                            @elseif($b->category === 'sports') <i class="fa-solid fa-dumbbell"></i>
                            @else <i class="fa-solid fa-building"></i>
                            @endif
                        </div>
                        <span class="font-mono" style="font-size: 0.75rem; font-weight: 700; background: rgba(0,0,0,0.8); padding: 0.15rem 0.5rem; border-radius: 4px; border: 1px solid rgba(255,255,255,0.2); white-space: nowrap;">
                            {{ $b->code }}
                        </span>
                    </div>

                    <!-- Building Card Hover Popup -->
                    <div class="building-card-popup glass-panel" style="display: none; position: absolute; bottom: 65px; left: 50%; transform: translateX(-50%); width: 280px; padding: 1.25rem; border-color: rgba(99, 102, 241, 0.5); box-shadow: 0 15px 35px rgba(0,0,0,0.8); z-index: 50;">
                        <div style="font-weight: 700; font-size: 1rem; color: #fff; margin-bottom: 0.25rem;">{{ $b->name }}</div>
                        <div style="font-size: 0.75rem; color: #38bdf8; margin-bottom: 0.5rem;">{{ $b->floors_count }} Mərtəbə • {{ $b->capacity }} Nəfərlik</div>
                        <p style="font-size: 0.8rem; color: #cbd5e1; line-height: 1.5; margin-bottom: 0.75rem;">{{ $b->description }}</p>
                        @if($b->facilities)
                            <div style="display: flex; flex-wrap: wrap; gap: 0.35rem;">
                                @foreach($b->facilities as $fac)
                                    <span style="font-size: 0.65rem; background: rgba(255,255,255,0.08); padding: 0.15rem 0.4rem; border-radius: 3px; color: #e2e8f0;">{{ $fac }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>
            @endforeach

        </div>

        <!-- Buildings Details Grid -->
        <h2 class="font-heading" style="font-size: 2rem; margin-bottom: 1.75rem;">Kampus Binaları və İnfrastruktur</h2>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.75rem;">
            @foreach($buildings as $b)
                <div class="glass-card" style="padding: 1.75rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                        <span class="font-mono badge badge-primary">{{ $b->code }}</span>
                        <span class="badge badge-accent">{{ strtoupper($b->category) }}</span>
                    </div>

                    <h3 class="font-heading" style="font-size: 1.25rem; margin-bottom: 0.5rem;">{{ $b->name }}</h3>
                    <p style="color: #94a3b8; font-size: 0.9rem; line-height: 1.6; margin-bottom: 1.25rem;">{{ $b->description }}</p>

                    <div style="display: flex; justify-content: space-between; font-size: 0.85rem; color: #cbd5e1; margin-bottom: 1rem; padding: 0.65rem; background: rgba(0,0,0,0.2); border-radius: 0.5rem;">
                        <span>Mərtəbə sayı: <strong>{{ $b->floors_count }}</strong></span>
                        <span>Tutum: <strong>{{ $b->capacity }} nəfər</strong></span>
                    </div>

                    @if($b->facilities)
                        <div style="display: flex; flex-wrap: wrap; gap: 0.4rem;">
                            @foreach($b->facilities as $f)
                                <span style="font-size: 0.75rem; background: rgba(99, 102, 241, 0.12); border: 1px solid rgba(99, 102, 241, 0.25); padding: 0.2rem 0.5rem; border-radius: 4px; color: #c7d2fe;">
                                    <i class="fa-solid fa-circle-dot" style="font-size: 0.5rem;"></i> {{ $f }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

    </div>

@endsection
