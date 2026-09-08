@extends('layouts.app')

@section('title', 'Universitet Tədbirləri və Konfranslar — Xəzər Universiteti')

@section('content')

    <!-- Header Banner -->
    <div style="background: radial-gradient(circle at 50% 0%, rgba(6, 182, 212, 0.25), transparent 70%); padding: 4rem 0 3rem; border-bottom: 1px solid rgba(255,255,255,0.06); text-align: center;">
        <div class="container">
            <span class="badge badge-accent" style="margin-bottom: 0.75rem;"><i class="fa-solid fa-calendar-days"></i> Tədbir Təqvimi</span>
            <h1 class="font-heading" style="font-size: 2.8rem; margin-bottom: 0.75rem;">Beynəlxalq Konfranslar və Sammitlər</h1>
            <p style="color: #94a3b8; font-size: 1.1rem; max-width: 650px; margin: 0 auto;">
                Dünya səviyyəli ekspertlər, texnologiya liderləri və karyera yarmarkaları ilə zəngin kampus həyatı.
            </p>
        </div>
    </div>

    <div class="container" style="padding: 4rem 1.5rem;">
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem;">
            @foreach($events as $event)
                <div class="glass-card" style="padding: 1.75rem; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <span class="badge badge-primary">{{ strtoupper($event->category) }}</span>
                            <div style="font-size: 0.8rem; color: #38bdf8; font-weight: 600;">
                                <i class="fa-solid fa-ticket"></i> {{ $event->registered_count }} / {{ $event->capacity }} Bilet
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                            <div style="background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.3); border-radius: 0.75rem; padding: 0.6rem 0.85rem; text-align: center; min-width: 65px;">
                                <div class="font-heading" style="font-size: 1.3rem; color: #818cf8; line-height: 1;">{{ $event->start_time->format('d') }}</div>
                                <div style="font-size: 0.7rem; color: #cbd5e1; font-weight: 700; text-transform: uppercase;">{{ $event->start_time->translatedFormat('M') }}</div>
                            </div>
                            <h3 class="font-heading" style="font-size: 1.2rem; line-height: 1.3;">
                                <a href="{{ route('events.show', $event->slug) }}" style="color: #fff; text-decoration: none;">
                                    {{ $event->title }}
                                </a>
                            </h3>
                        </div>

                        <p style="color: #94a3b8; font-size: 0.85rem; line-height: 1.6; margin-bottom: 1.25rem;">
                            {{ Str::limit($event->description, 110) }}
                        </p>

                        <div style="font-size: 0.8rem; color: #cbd5e1; margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 0.35rem;">
                            <div><i class="fa-solid fa-location-dot text-indigo-400"></i> {{ $event->venue }}</div>
                            <div><i class="fa-solid fa-user-group text-cyan-400"></i> {{ Str::limit($event->speaker_names, 40) }}</div>
                        </div>
                    </div>

                    <a href="{{ route('events.show', $event->slug) }}" class="btn gradient-btn" style="width: 100%; padding: 0.6rem; font-size: 0.85rem;">
                        Təfərrüat və Bilet Al <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 2.5rem;">
            {{ $events->links() }}
        </div>
    </div>

@endsection
