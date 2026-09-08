@extends('layouts.app')

@section('title', 'Universitet Xəbərləri və Mətbuat — Xəzər Universiteti')

@section('content')

    <!-- Header Banner -->
    <div style="background: radial-gradient(circle at 50% 0%, rgba(99, 102, 241, 0.2), transparent 70%); padding: 4rem 0 3rem; border-bottom: 1px solid rgba(255,255,255,0.06); text-align: center;">
        <div class="container">
            <span class="badge badge-accent" style="margin-bottom: 0.75rem;"><i class="fa-solid fa-newspaper"></i> Mətbuat Xidməti</span>
            <h1 class="font-heading" style="font-size: 2.8rem; margin-bottom: 0.75rem;">Xəbərlər və Nailiyyətlər</h1>
            <p style="color: #94a3b8; font-size: 1.1rem; max-width: 650px; margin: 0 auto;">
                Universitetimizin elmi kəşfləri, beynəlxalq əlaqələri və tələbə həyatından ən son yeniliklər.
            </p>
        </div>
    </div>

    <div class="container" style="padding: 4rem 1.5rem;">
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem;">
            @foreach($news as $article)
                <div class="glass-card" style="padding: 1.75rem; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                            <span class="badge badge-primary">{{ strtoupper($article->category) }}</span>
                            <span style="font-size: 0.8rem; color: #94a3b8;"><i class="fa-regular fa-clock"></i> {{ $article->published_at?->format('d.m.Y') }}</span>
                        </div>

                        <h3 class="font-heading" style="font-size: 1.25rem; line-height: 1.35; margin-bottom: 0.75rem;">
                            <a href="{{ route('news.show', $article->slug) }}" style="color: #fff; text-decoration: none;">
                                {{ $article->title }}
                            </a>
                        </h3>

                        <p style="color: #94a3b8; font-size: 0.85rem; line-height: 1.6; margin-bottom: 1.25rem;">
                            {{ $article->summary }}
                        </p>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.06); font-size: 0.85rem;">
                        <span style="color: #94a3b8;"><i class="fa-solid fa-eye"></i> {{ $article->views_count }} Baxış</span>
                        <a href="{{ route('news.show', $article->slug) }}" style="color: #818cf8; text-decoration: none; font-weight: 600;">
                            Ətraflı Oxu <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 2.5rem;">
            {{ $news->links() }}
        </div>
    </div>

@endsection
