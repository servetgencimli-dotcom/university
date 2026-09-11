@extends('layouts.portal')

@section('portal_title', 'Bildiriş Mərkəzi | Xəzər Universiteti')

@section('portal_content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Bildiriş Mərkəzi</h1>
            <p class="text-sm text-slate-500">Bütün akademik xəbərdarlıqlar, elanlar və sistem yenilikləri</p>
        </div>
        <form method="POST" action="{{ route('portal.notifications.readAll') }}">
            @csrf
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-slate-300 rounded-xl text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 shadow-sm transition">
                <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Hamısını oxunmuş kimi qeyd et
            </button>
        </form>
    </div>

    @if($notifications->isEmpty())
        <div class="bg-white rounded-2xl p-12 text-center border border-slate-200">
            <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-1">Yeni bildiriş yoxdur</h3>
            <p class="text-sm text-slate-500">Hazırda sizə aid oxunmamış bildiriş tapılmadı.</p>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 divide-y divide-slate-100 overflow-hidden">
            @foreach($notifications as $notif)
                <div class="p-5 flex items-start justify-between transition {{ $notif->is_read ? 'bg-white hover:bg-slate-50/50' : 'bg-blue-50/30 hover:bg-blue-50/60 font-medium' }}">
                    <div class="flex items-start space-x-4">
                        <div class="mt-1 flex-shrink-0">
                            @if($notif->type === 'academic_warning' || $notif->type === 'danger')
                                <span class="w-9 h-9 rounded-xl bg-red-100 text-red-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </span>
                            @elseif($notif->type === 'success' || $notif->type === 'payment')
                                <span class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </span>
                            @elseif($notif->type === 'warning')
                                <span class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </span>
                            @else
                                <span class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </span>
                            @endif
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <h4 class="text-sm font-bold text-slate-800">{{ $notif->title }}</h4>
                                @if(!$notif->is_read)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-blue-600 text-white">YENİ</span>
                                @endif
                            </div>
                            <p class="text-sm text-slate-600 mt-1 leading-relaxed">{{ $notif->message }}</p>
                            <div class="flex items-center space-x-4 mt-2 text-xs text-slate-400">
                                <span>{{ $notif->created_at->format('d.m.Y H:i') }} ({{ $notif->created_at->diffForHumans() }})</span>
                                @if($notif->link)
                                    <a href="{{ $notif->link }}" class="text-blue-600 hover:text-blue-800 font-semibold hover:underline">Ətraflı keçid &rarr;</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if(!$notif->is_read)
                        <form method="POST" action="{{ route('portal.notifications.read', $notif->id) }}">
                            @csrf
                            <button type="submit" class="text-xs text-slate-400 hover:text-blue-600 font-medium px-2 py-1 rounded hover:bg-white border border-transparent hover:border-slate-200 transition" title="Oxunmuş et">
                                Oxundu
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
