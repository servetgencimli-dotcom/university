@extends('layouts.app')

@section('title', "{$event->title} — Xəzər Universiteti")

@section('content')

    <!-- Event Header -->
    <div style="background: radial-gradient(circle at 50% 0%, rgba(99, 102, 241, 0.25), transparent 70%); padding: 4.5rem 0 3.5rem; border-bottom: 1px solid rgba(255,255,255,0.08);">
        <div class="container">
            <div style="display: flex; gap: 0.5rem; font-size: 0.85rem; color: #94a3b8; margin-bottom: 1rem;">
                <a href="{{ route('home') }}" style="color: #94a3b8; text-decoration: none;">Əsas</a>
                <span>/</span>
                <a href="{{ route('events.index') }}" style="color: #94a3b8; text-decoration: none;">Tədbirlər</a>
                <span>/</span>
                <span style="color: #818cf8;">Təfərrüat</span>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 3.5rem; align-items: center;">
                <div>
                    <span class="badge badge-accent" style="margin-bottom: 0.75rem;">{{ strtoupper($event->category) }}</span>
                    <h1 class="font-heading" style="font-size: 2.6rem; line-height: 1.2; margin-bottom: 1.25rem;">{{ $event->title }}</h1>
                    
                    <div style="display: flex; flex-wrap: wrap; gap: 1.5rem; font-size: 0.95rem; color: #cbd5e1; margin-bottom: 1.5rem;">
                        <div><i class="fa-solid fa-calendar text-indigo-400"></i> {{ $event->start_time->translatedFormat('d F Y, H:i') }}</div>
                        <div><i class="fa-solid fa-location-dot text-indigo-400"></i> {{ $event->venue }}</div>
                    </div>

                    <p style="color: #cbd5e1; font-size: 1.05rem; line-height: 1.7;">
                        {{ $event->description }}
                    </p>
                </div>

                <!-- RSVP Ticket Booking Box -->
                <div class="glass-panel" style="padding: 2rem; border-color: rgba(6, 182, 212, 0.4);">
                    <h3 class="font-heading" style="font-size: 1.4rem; margin-bottom: 0.5rem; color: #fff;">
                        <i class="fa-solid fa-ticket text-cyan-400"></i> Rəqəmsal Bilet Rezervasiyası
                    </h3>
                    <p style="font-size: 0.85rem; color: #94a3b8; margin-bottom: 1.25rem;">
                        Tədbirə qeydiyyat ödənişsizdir. Təsdiq QR kodu dərhal generasiya ediləcək.
                    </p>

                    <form action="{{ route('events.rsvp', $event->slug) }}" method="POST">
                        @csrf
                        <div style="margin-bottom: 1rem;">
                            <label style="font-size: 0.8rem; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Ad və Soyadınız *</label>
                            <input type="text" name="full_name" required value="{{ Auth::user()->name ?? '' }}" placeholder="Məs: Murad Hüseynov" class="form-input">
                        </div>

                        <div style="margin-bottom: 1.5rem;">
                            <label style="font-size: 0.8rem; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Elektron Poçt (Email) *</label>
                            <input type="email" name="email" required value="{{ Auth::user()->email ?? '' }}" placeholder="example@mail.com" class="form-input">
                        </div>

                        <button type="submit" class="btn gradient-btn" style="width: 100%; padding: 0.8rem; font-size: 0.95rem;">
                            <i class="fa-solid fa-qrcode"></i> Bilet Rezervasiya Et
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- Details Column -->
    <div class="container" style="padding: 3.5rem 1.5rem;">
        <h3 class="font-heading" style="font-size: 1.5rem; margin-bottom: 1rem;">Dəvət Olunmuş Qonaqlar və Spikerlər</h3>
        <div class="glass-card" style="padding: 1.5rem; max-width: 800px; color: #cbd5e1; line-height: 1.7;">
            {{ $event->speaker_names ?? 'Beynəlxalq ekspertlər və professor heyəti' }}
        </div>
    </div>

@endsection
