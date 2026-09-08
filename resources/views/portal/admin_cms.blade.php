@extends('layouts.portal')

@section('portal_title', 'CMS və Akademik İdarəetmə | Xəzər Universiteti')

@section('portal_content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">CMS və Akademik Parametrlər</h1>
            <p class="text-sm text-slate-500">Qeydiyyat pəncərəsinin idarəsi, xəbərlər, tədbirlər və akademik fənlərin CMS idarə paneli</p>
        </div>
    </div>

    <!-- Term & Registration Window Control -->
    <div class="bg-gradient-to-br from-slate-900 to-blue-950 text-white p-6 rounded-3xl shadow-xl">
        <h2 class="text-lg font-bold text-blue-200 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            Akademik Semestrlər & Fənn Qeydiyyatı Pəncərəsi (Self-Service Registration Window)
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($terms as $term)
                <div class="bg-white/10 backdrop-blur-sm border border-white/15 p-5 rounded-2xl flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-base font-bold text-white">{{ $term->name }} ({{ $term->code }})</h3>
                            @if($term->is_current)
                                <span class="px-2 py-0.5 text-xs font-bold bg-emerald-500 text-white rounded-md">Cari Semestr</span>
                            @endif
                        </div>
                        <p class="text-xs text-blue-200 mb-3 font-mono">
                            {{ $term->start_date ? $term->start_date->format('d.m.Y') : '-' }} — {{ $term->end_date ? $term->end_date->format('d.m.Y') : '-' }}
                        </p>
                        <div class="flex items-center space-x-2 text-xs">
                            <span class="text-slate-300">Qeydiyyat:</span>
                            @if($term->is_registration_open)
                                <span class="text-emerald-400 font-bold flex items-center"><span class="w-2 h-2 rounded-full bg-emerald-400 mr-1.5 animate-pulse"></span> AÇIQ</span>
                            @else
                                <span class="text-red-400 font-bold">BAĞLI</span>
                            @endif
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-white/10 flex items-center space-x-2">
                        <form method="POST" action="{{ route('portal.admin_cms.terms.toggle_registration', $term->id) }}" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full py-2 px-3 text-xs font-bold rounded-xl transition {{ $term->is_registration_open ? 'bg-red-600/80 hover:bg-red-600 text-white' : 'bg-emerald-600/90 hover:bg-emerald-600 text-white' }}">
                                {{ $term->is_registration_open ? 'Qeydiyyatı Bağla' : 'Qeydiyyatı Aç' }}
                            </button>
                        </form>
                        @if(!$term->is_current)
                            <form method="POST" action="{{ route('portal.admin_cms.terms.set_current', $term->id) }}">
                                @csrf
                                <button type="submit" class="py-2 px-3 text-xs font-bold bg-white/20 hover:bg-white/30 text-white rounded-xl transition" title="Cari semestr et">
                                    Cari Et
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Tabs for Content Creation -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- News Creation Form -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                Yeni Universitet Xəbəri Dərc Et
            </h2>
            <form method="POST" action="{{ route('portal.admin_cms.news.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Xəbər Başlığı *</label>
                    <input type="text" name="title" required class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Kateqoriya *</label>
                        <select name="category" required class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="Akademik">Akademik</option>
                            <option value="Tədqiqat">Tədqiqat</option>
                            <option value="Tələbə Həyatı">Tələbə Həyatı</option>
                            <option value="Beynəlxalq">Beynəlxalq</option>
                            <option value="Təqaüd">Təqaüd</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Nəşr Tarixi *</label>
                        <input type="date" name="published_at" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Qısa Xülasə (Excerpt) *</label>
                    <textarea name="excerpt" rows="2" required class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Tam Məzmun (HTML/Text) *</label>
                    <textarea name="body" rows="4" required class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <button type="submit" class="w-full py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700 transition">
                    Xəbəri Dərc Et
                </button>
            </form>
        </div>

        <!-- Event Creation Form -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Yeni Universitet Tədbiri Əlavə Et
            </h2>
            <form method="POST" action="{{ route('portal.admin_cms.events.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Tədbirin Adı *</label>
                    <input type="text" name="title" required class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Məkan (Location) *</label>
                        <input type="text" name="location" placeholder="Məs. Mərmər Salon, Neftçilər Korpusu" required class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Kateqoriya *</label>
                        <select name="category" required class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="Konfrans">Konfrans</option>
                            <option value="Seminar">Seminar</option>
                            <option value="Mədəniyyət">Mədəniyyət</option>
                            <option value="Karyera">Karyera</option>
                            <option value="İdman">İdman</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Başlama Vaxtı *</label>
                        <input type="datetime-local" name="starts_at" required class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Bitmə Vaxtı</label>
                        <input type="datetime-local" name="ends_at" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Təsvir *</label>
                    <textarea name="description" rows="3" required class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <button type="submit" class="w-full py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-semibold hover:bg-indigo-700 transition">
                    Tədbiri Yarat
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
