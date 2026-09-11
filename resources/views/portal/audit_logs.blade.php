@extends('layouts.portal')

@section('portal_title', 'Təhlükəsizlik və Audit Jurnalı | Xəzər Universiteti')

@section('portal_content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Təhlükəsizlik və Audit Jurnalı (Audit Logs)</h1>
            <p class="text-sm text-slate-500">Sistemdə baş verən bütün kritik əməliyyatların (qiymətləndirmə, rol təyini, ödəniş, fənn seçimi və s.) təftiş qeydləri</p>
        </div>
        <div class="flex items-center space-x-2">
            <span class="px-3 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl text-xs font-semibold flex items-center">
                <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span> Canlı İzləmə Aktivdir
            </span>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <form method="GET" action="{{ route('portal.admin.audit-logs') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Açar Söz / Təsvir</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Axtarış..." class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Əməliyyat (Action)</label>
                <select name="action" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">Bütün əməliyyatlar</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>{{ $act }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Model / Bölmə</label>
                <select name="target_type" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">Bütün modellər</option>
                    @foreach($targetTypes as $tt)
                        <option value="{{ $tt }}" {{ request('target_type') == $tt ? 'selected' : '' }}>{{ class_basename($tt) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700 transition">
                    Filtri tətbiq et
                </button>
                <a href="{{ route('portal.admin.audit-logs') }}" class="px-4 py-2 border border-slate-300 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Sıfırla
                </a>
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Vaxt</th>
                        <th class="px-6 py-3.5">İstifadəçi (İcraçı)</th>
                        <th class="px-6 py-3.5">Əməliyyat</th>
                        <th class="px-6 py-3.5">Hədəf Obyekt</th>
                        <th class="px-6 py-3.5">Təsvir & Detallar</th>
                        <th class="px-6 py-3.5">IP Ünvan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500 font-mono">
                                {{ $log->created_at->format('d.m.Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($log->user)
                                    <div class="font-bold text-slate-800">{{ $log->user->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $log->user->email }}</div>
                                @else
                                    <span class="text-xs text-slate-400 font-mono">Sistem / Qonaq</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold 
                                    @if(str_contains($log->action, 'create') || str_contains($log->action, 'enroll') || str_contains($log->action, 'admit')) bg-emerald-100 text-emerald-800
                                    @elseif(str_contains($log->action, 'delete') || str_contains($log->action, 'drop')) bg-red-100 text-red-800
                                    @elseif(str_contains($log->action, 'password') || str_contains($log->action, 'impersonate')) bg-purple-100 text-purple-800
                                    @elseif(str_contains($log->action, 'payment')) bg-amber-100 text-amber-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs font-mono text-slate-600">
                                @if($log->target_type)
                                    {{ class_basename($log->target_type) }} #{{ $log->target_id }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-xs text-slate-700 font-medium">{{ $log->description }}</p>
                                @if($log->details && count($log->details) > 0)
                                    <details class="mt-1">
                                        <summary class="text-[11px] text-blue-600 cursor-pointer hover:underline font-mono">JSON detallara bax</summary>
                                        <pre class="mt-1 p-2 bg-slate-900 text-emerald-400 text-[10px] rounded-lg overflow-x-auto max-w-md font-mono">{{ json_encode($log->details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    </details>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs font-mono text-slate-500">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                Audit qeydi tapılmadı.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
