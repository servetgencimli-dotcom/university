@extends('layouts.app')

@section('title', 'Elmi Tədqiqatlar və Nəşrlər — Xəzər Universiteti')

@section('content')

    <!-- Header Banner -->
    <div style="background: radial-gradient(circle at 50% 0%, rgba(99, 102, 241, 0.25), transparent 70%); padding: 4rem 0 3rem; border-bottom: 1px solid rgba(255,255,255,0.06); text-align: center;">
        <div class="container">
            <span class="badge badge-accent" style="margin-bottom: 0.75rem;"><i class="fa-solid fa-atom"></i> Elm və İxtira Mərkəzi</span>
            <h1 class="font-heading" style="font-size: 2.8rem; margin-bottom: 0.75rem;">Elmi Tədqiqatlar və Beynəlxalq Nəşrlər</h1>
            <p style="color: #94a3b8; font-size: 1.1rem; max-width: 650px; margin: 0 auto;">
                IEEE, Nature və ACM jurnallarında indekslənən professor və gənc tədqiqatçılarımızın elmi məqalələri.
            </p>
        </div>
    </div>

    <div class="container" style="padding: 3.5rem 1.5rem;">
        
        <!-- Search & Category Filters -->
        <div class="glass-panel" style="padding: 1.5rem; margin-bottom: 2.5rem;">
            <form action="{{ route('research.index') }}" method="GET" style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 1rem;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Məqalə başlığı, açar söz və ya müəllif..." class="form-input">
                <select name="category" class="form-select">
                    <option value="">Bütün Kateqoriyalar</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn gradient-btn" style="padding: 0 1.5rem;">
                    <i class="fa-solid fa-magnifying-glass"></i> Axtar
                </button>
            </form>
        </div>

        <!-- Papers List -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            @foreach($papers as $paper)
                <div class="glass-card" style="padding: 2rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                        <span class="badge badge-accent">{{ $paper->category }}</span>
                        <div style="display: flex; gap: 1rem; font-size: 0.85rem; color: #94a3b8;">
                            <span><i class="fa-solid fa-calendar"></i> {{ $paper->publication_date?->format('Y') ?? '2026' }}</span>
                            <span><i class="fa-solid fa-quote-right text-indigo-400"></i> {{ $paper->citations_count }} Sitat</span>
                        </div>
                    </div>

                    <h3 class="font-heading" style="font-size: 1.4rem; line-height: 1.3; margin-bottom: 0.65rem; color: #fff;">
                        {{ $paper->title }}
                    </h3>

                    <div style="font-size: 0.9rem; color: #818cf8; margin-bottom: 1rem;">
                        <strong>Müəllif(lər):</strong> {{ $paper->author?->name ?? 'Professor' }}
                        @if($paper->co_authors), {{ $paper->co_authors }} @endif
                        • <span style="color: #cbd5e1; font-style: italic;">{{ $paper->journal_name }}</span>
                    </div>

                    <p style="color: #cbd5e1; font-size: 0.95rem; line-height: 1.7; margin-bottom: 1.25rem;">
                        {{ $paper->abstract }}
                    </p>

                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.06);">
                        <div class="font-mono" style="font-size: 0.8rem; color: #94a3b8;">
                            DOI: {{ $paper->doi ?? '10.5782/KHAZAR.2026.01' }}
                        </div>
                        <a href="https://doi.org/{{ $paper->doi }}" target="_blank" class="btn btn-outline" style="padding: 0.4rem 1rem; font-size: 0.85rem;">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Nəşri Oxu (DOI)
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 2.5rem;">
            {{ $papers->links() }}
        </div>

    </div>

@endsection
