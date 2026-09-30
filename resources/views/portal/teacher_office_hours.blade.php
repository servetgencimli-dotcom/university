@extends('layouts.portal')

@section('title', 'Qəbul Saatları İdarəetməsi — Xəzər Universiteti')
@section('header_title', 'Müəllim Qəbul Saatları (Office Hours)')

@section('content')

    {{-- Top Alert Messages --}}
    @if(session('success'))
        <div class="glass-panel" style="padding: 1rem 1.25rem; margin-bottom: 1.5rem; border-left: 4px solid #10b981; background: rgba(16, 185, 129, 0.1); color: #6ee7b7; border-radius: 12px;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span style="font-weight: 500;">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="glass-panel" style="padding: 1rem 1.25rem; margin-bottom: 1.5rem; border-left: 4px solid #ef4444; background: rgba(239, 68, 68, 0.1); color: #fca5a5; border-radius: 12px;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span style="font-weight: 500;">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- Header Banner --}}
    <div class="glass-panel" style="padding: 1.75rem; margin-bottom: 2rem; border-radius: 16px; background: linear-gradient(135deg, rgba(30, 41, 59, 0.8), rgba(15, 23, 42, 0.9)); border: 1px solid rgba(255, 255, 255, 0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="font-size: 1.5rem; font-weight: 700; color: #fff; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 0.75rem;">
                    <span>💬</span> Qəbul Saatları və Tələbə Konsultasiya Konsolu
                </h2>
                <p style="color: #94a3b8; margin: 0; font-size: 0.925rem;">
                    Həftəlik dərslərdən kənar tələbə qəbul pəncərələrini təyin edin, görüş müraciətlərini təsdiqləyin.
                </p>
            </div>
            <div>
                <button onclick="document.getElementById('newSlotModal').style.display='flex'" class="btn btn-primary" style="padding: 0.75rem 1.25rem; border-radius: 10px; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #3b82f6, #2563eb);">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Yeni Qəbul Vaxtı Əlavə Et
                </button>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
        
        {{-- Left Side: Active Slot Windows --}}
        <div>
            <div class="glass-panel" style="padding: 1.5rem; border-radius: 16px; height: 100%;">
                <h3 style="font-size: 1.1rem; font-weight: 700; color: #f8fafc; margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: space-between;">
                    <span>🕒 Təyin Olunmuş Qəbul Vaxtları</span>
                    <span style="font-size: 0.8rem; background: rgba(59, 130, 246, 0.2); color: #60a5fa; padding: 0.25rem 0.6rem; border-radius: 20px;">{{ $slots->count() }} pəncərə</span>
                </h3>

                @if($slots->isEmpty())
                    <div style="text-align: center; padding: 2.5rem 1rem; color: #64748b;">
                        <svg style="width: 40px; height: 40px; margin: 0 auto 0.75rem; opacity: 0.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p style="margin: 0; font-size: 0.9rem;">Hələ heç bir qəbul vaxtı təyin edilməyib.</p>
                    </div>
                @else
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        @foreach($slots as $slot)
                            <div style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 12px; padding: 1rem;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                    <div>
                                        <span style="font-weight: 700; color: #38bdf8; font-size: 1rem;">{{ $slot->day_of_week }}</span>
                                        <div style="color: #e2e8f0; font-size: 0.95rem; font-weight: 600; margin-top: 0.2rem;">
                                            {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}
                                        </div>
                                    </div>
                                    <form action="{{ route('portal.teacher.office-hours.slot.delete', $slot->id) }}" method="POST" onsubmit="return confirm('Bu qəbul vaxtını silmək istədiyinizdən əminsiniz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer; padding: 4px; border-radius: 6px;" title="Sil">
                                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                                <div style="font-size: 0.825rem; color: #94a3b8; display: flex; flex-direction: column; gap: 0.25rem;">
                                    <div>📍 {{ $slot->location ?? 'Otaq göstərilməyib' }}</div>
                                    @if($slot->zoom_link)
                                        <div>💻 <a href="{{ $slot->zoom_link }}" target="_blank" style="color: #60a5fa; text-decoration: underline;">Zoom Linki</a></div>
                                    @endif
                                    <div style="display: flex; justify-content: space-between; margin-top: 0.4rem; padding-top: 0.4rem; border-top: 1px dashed rgba(255,255,255,0.08);">
                                        <span>Limit: {{ $slot->max_students_per_slot }} tələbə/slot</span>
                                        <span style="color: #34d399;">{{ $slot->bookings_count }} müraciət</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Right Side: Student Bookings Table --}}
        <div>
            <div class="glass-panel" style="padding: 1.5rem; border-radius: 16px;">
                <h3 style="font-size: 1.1rem; font-weight: 700; color: #f8fafc; margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: space-between;">
                    <span>📋 Tələbə Müraciətləri və Görüş Siyahısı</span>
                    <span style="font-size: 0.8rem; background: rgba(16, 185, 129, 0.2); color: #34d399; padding: 0.25rem 0.6rem; border-radius: 20px;">Cəmi {{ $bookings->count() }} müraciət</span>
                </h3>

                @if($bookings->isEmpty())
                    <div style="text-align: center; padding: 3rem 1rem; color: #64748b;">
                        <p style="margin: 0; font-size: 0.95rem;">Hələ ki, heç bir tələbə görüş müraciəti etməyib.</p>
                    </div>
                @else
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
                            <thead>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.1); color: #94a3b8;">
                                    <th style="padding: 0.75rem 1rem;">Tələbə</th>
                                    <th style="padding: 0.75rem 1rem;">Tarix & Saat</th>
                                    <th style="padding: 0.75rem 1rem;">Mövzu / Səbəb</th>
                                    <th style="padding: 0.75rem 1rem;">Status</th>
                                    <th style="padding: 0.75rem 1rem; text-align: right;">Əməliyyat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                        <td style="padding: 1rem;">
                                            <div style="font-weight: 600; color: #f8fafc;">{{ $booking->student->name }}</div>
                                            <div style="font-size: 0.75rem; color: #64748b;">{{ $booking->student->email }}</div>
                                        </td>
                                        <td style="padding: 1rem; color: #cbd5e1;">
                                            <div>📅 {{ \Carbon\Carbon::parse($booking->booking_date)->format('d.m.Y') }}</div>
                                            <div style="font-size: 0.8rem; color: #38bdf8;">
                                                {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                            </div>
                                        </td>
                                        <td style="padding: 1rem; color: #e2e8f0; max-width: 200px;">
                                            {{ Str::limit($booking->topic, 60) }}
                                        </td>
                                        <td style="padding: 1rem;">
                                            @if($booking->status == 'pending')
                                                <span style="background: rgba(234, 179, 8, 0.2); color: #facc15; padding: 0.25rem 0.6rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">Gözləmədə</span>
                                            @elseif($booking->status == 'confirmed')
                                                <span style="background: rgba(59, 130, 246, 0.2); color: #60a5fa; padding: 0.25rem 0.6rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">Təsdiqləndi</span>
                                            @elseif($booking->status == 'completed')
                                                <span style="background: rgba(16, 185, 129, 0.2); color: #34d399; padding: 0.25rem 0.6rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">Tamamlandı</span>
                                            @else
                                                <span style="background: rgba(239, 68, 68, 0.2); color: #fca5a5; padding: 0.25rem 0.6rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">Ləğv Edildi</span>
                                            @endif
                                        </td>
                                        <td style="padding: 1rem; text-align: right;">
                                            <div style="display: flex; justify-content: flex-end; gap: 0.35rem;">
                                                @if($booking->status == 'pending')
                                                    <form action="{{ route('portal.teacher.office-hours.booking.status', $booking->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="confirmed">
                                                        <button type="submit" style="background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.4); border-radius: 6px; padding: 4px 8px; font-size: 0.75rem; cursor: pointer;">
                                                            Təsdiqlə
                                                        </button>
                                                    </form>
                                                @endif
                                                @if($booking->status != 'completed' && $booking->status != 'cancelled')
                                                    <form action="{{ route('portal.teacher.office-hours.booking.status', $booking->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="completed">
                                                        <button type="submit" style="background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4); border-radius: 6px; padding: 4px 8px; font-size: 0.75rem; cursor: pointer;">
                                                            Tamamlandı
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- New Slot Modal --}}
    <div id="newSlotModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center;">
        <div class="glass-panel" style="width: 100%; max-width: 520px; padding: 2rem; border-radius: 20px; background: #0f172a; border: 1px solid rgba(255,255,255,0.15);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.2rem; font-weight: 700; color: #fff; margin: 0;">➕ Yeni Qəbul Vaxtı Pəncərəsi</h3>
                <button onclick="document.getElementById('newSlotModal').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.25rem; cursor: pointer;">✕</button>
            </div>

            <form action="{{ route('portal.teacher.office-hours.slot.store') }}" method="POST">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div style="grid-column: span 2;">
                        <label style="display: block; font-size: 0.85rem; color: #94a3b8; margin-bottom: 0.4rem;">Həftənin Günü</label>
                        <select name="day_of_week" required style="width: 100%; background: #1e293b; border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 0.6rem 0.8rem; border-radius: 8px;">
                            <option value="Bazar ertəsi">Bazar ertəsi</option>
                            <option value="Çərşənbə axşamı">Çərşənbə axşamı</option>
                            <option value="Çərşənbə">Çərşənbə</option>
                            <option value="Cümə axşamı">Cümə axşamı</option>
                            <option value="Cümə">Cümə</option>
                            <option value="Şənbə">Şənbə</option>
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.85rem; color: #94a3b8; margin-bottom: 0.4rem;">Başlama Saatı</label>
                        <input type="time" name="start_time" required value="14:00" style="width: 100%; background: #1e293b; border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 0.6rem 0.8rem; border-radius: 8px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.85rem; color: #94a3b8; margin-bottom: 0.4rem;">Bitmə Saatı</label>
                        <input type="time" name="end_time" required value="16:00" style="width: 100%; background: #1e293b; border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 0.6rem 0.8rem; border-radius: 8px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.85rem; color: #94a3b8; margin-bottom: 0.4rem;">Müddət (dəqiqə)</label>
                        <input type="number" name="slot_duration_minutes" value="20" required style="width: 100%; background: #1e293b; border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 0.6rem 0.8rem; border-radius: 8px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.85rem; color: #94a3b8; margin-bottom: 0.4rem;">Maks Tələbə Sayı</label>
                        <input type="number" name="max_students_per_slot" value="3" required style="width: 100%; background: #1e293b; border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 0.6rem 0.8rem; border-radius: 8px;">
                    </div>

                    <div style="grid-column: span 2;">
                        <label style="display: block; font-size: 0.85rem; color: #94a3b8; margin-bottom: 0.4rem;">Fiziki Məkan (Otaq nömrəsi)</label>
                        <input type="text" name="location" placeholder="Məs: Otaq 402, Əsas Korpus" style="width: 100%; background: #1e293b; border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 0.6rem 0.8rem; border-radius: 8px;">
                    </div>

                    <div style="grid-column: span 2;">
                        <label style="display: block; font-size: 0.85rem; color: #94a3b8; margin-bottom: 0.4rem;">Onlayn Zoom Linki (istəyə bağlı)</label>
                        <input type="url" name="zoom_link" placeholder="https://zoom.us/j/..." style="width: 100%; background: #1e293b; border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 0.6rem 0.8rem; border-radius: 8px;">
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                    <button type="button" onclick="document.getElementById('newSlotModal').style.display='none'" class="btn" style="background: rgba(255,255,255,0.1); color: #fff;">Ləğv et</button>
                    <button type="submit" class="btn btn-primary" style="background: #3b82f6;">Yadda Saxla</button>
                </div>
            </form>
        </div>
    </div>

@endsection
