@extends('layouts.app')

@section('title', "{$article->title} — Xəzər Universiteti")

@section('content')

    <!-- Article Header -->
    <div style="background: radial-gradient(circle at 50% 0%, rgba(99, 102, 241, 0.25), transparent 70%); padding: 4.5rem 0 3.5rem; border-bottom: 1px solid rgba(255,255,255,0.08);">
        <div class="container" style="max-width: 900px;">
            <div style="display: flex; gap: 0.5rem; font-size: 0.85rem; color: #94a3b8; margin-bottom: 1rem;">
                <a href="{{ route('home') }}" style="color: #94a3b8; text-decoration: none;">Əsas</a>
                <span>/</span>
                <a href="{{ route('news.index') }}" style="color: #94a3b8; text-decoration: none;">Xəbərlər</a>
                <span>/</span>
                <span style="color: #818cf8;">Məqalə</span>
            </div>

            <span class="badge badge-accent" style="margin-bottom: 0.75rem;">{{ strtoupper($article->category) }}</span>
            <h1 class="font-heading" style="font-size: 2.6rem; line-height: 1.25; margin-bottom: 1.25rem;">{{ $article->title }}</h1>

            <div style="display: flex; gap: 1.5rem; font-size: 0.9rem; color: #94a3b8;">
                <div><i class="fa-regular fa-calendar"></i> {{ $article->published_at?->translatedFormat('d F Y') }}</div>
                <div><i class="fa-regular fa-eye"></i> {{ $article->views_count }} Baxış</div>
            </div>
        </div>
    </div>

    <!-- Article Content -->
    <div class="container" style="padding: 4rem 1.5rem; max-width: 900px;">
        <div class="glass-panel" style="padding: 3rem; font-size: 1.1rem; line-height: 1.9; color: #e2e8f0; margin-bottom: 3.5rem;">
            {!! $article->content !!}
        </div>

        <!-- Recent Articles -->
        <h3 class="font-heading" style="font-size: 1.6rem; margin-bottom: 1.5rem;">Digər Xəbərlər</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            @foreach($recentNews as $rec)
                <div class="glass-card" style="padding: 1.25rem;">
                    <div style="font-size: 0.75rem; color: #38bdf8; margin-bottom: 0.25rem;">{{ $rec->published_at?->format('d.m.Y') }}</div>
                    <h4 class="font-heading" style="font-size: 1.05rem;">
                        <a href="{{ route('news.show', $rec->slug) }}" style="color: #fff; text-decoration: none;">
                            {{ $rec->title }}
                        </a>
                    </h4>
                </div>
            @endforeach
        </div>
    </div>

@endsection
