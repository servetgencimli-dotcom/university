@extends('layouts.portal')

@section('title', 'Müəllim Qəbuluna Yazılmaq — Xəzər Universiteti')
@section('header_title', 'Müəllim Qəbulu və Konsultasiya')

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
        <h2 style="font-size: 1.5rem; font-weight: 700; color: #fff; margin: 0 0 0.5rem 0;">
            📅 Müəllim Qəbul Saatlarına (Office Hours) Onlayn Yazılma
        </h2>
        <p style="color: #94a3b8; margin: 0; font-size: 0.925rem;">
            Fənn müəllimlərinizlə fərdi konsultasiya və ya akademik suallarınız üçün qəbul vaxtı seçin.
        </p>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">

        {{-- Left: Available Slots Grid --}}
        <div>
            <div class="glass-panel" style="padding: 1.5rem; border-radius: 16px; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; color: #f8fafc; margin-bottom: 1.25rem;">
                    💡 Əlçatan Müəllim Qəbul Pəncərələri
                </h3>

                @if($availableSlots->isEmpty())
                    <div style="text-align: center; padding: 3rem 1rem; color: #64748b;">
                        <p style="margin: 0; font-size: 0.95rem;">Hal-hazırda fənn müəllimləriniz tərəfindən aktiv qəbul saati açılmayıb.</p>
                    </div>
                @else
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem;">
                        @foreach($availableSlots as $slot)
                            <div style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 12px; padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #1d4ed8); display: flex; align-items: center; justify-content: center; font-weight: 700; color: #fff;">
                                            {{ strtoupper(substr($slot->teacher->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: #fff; font-size: 0.95rem;">{{ $slot->teacher->name }}</div>
                                            <div style="font-size: 0.75rem; color: #38bdf8;">Müəllim</div>
                                        </div>
                                    </div>

                                    <div style="font-size: 0.875rem; color: #cbd5e1; margin-bottom: 0.75rem;">
                                        <div>📅 <strong style="color: #60a5fa;">{{ $slot->day_of_week }}</strong></div>
                                        <div>⏰ {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }} ({{ $slot->slot_duration_minutes }} dəq)</div>
                                        <div style="color: #94a3b8; font-size: 0.8rem; margin-top: 0.25rem;">📍 {{ $slot->location ?? 'Otaq / Online' }}</div>
                                    </div>
                                </div>

                                <button onclick="openBookingModal({{ $slot->id }}, '{{ $slot->teacher->name }}', '{{ $slot->day_of_week }}', '{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}')" class="btn btn-primary" style="width: 100%; padding: 0.6rem; border-radius: 8px; font-size: 0.85rem; font-weight: 600; background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.4);">
                                    Görüş Təyin Et
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Right: My Bookings --}}
        <div>
            <div class="glass-panel" style="padding: 1.5rem; border-radius: 16px;">
                <h3 style="font-size: 1.1rem; font-weight: 700; color: #f8fafc; margin-bottom: 1.25rem;">
                    📌 Mənim Müraciətlərim
                </h3>

                @if($myBookings->isEmpty())
                    <div style="text-align: center; padding: 2rem 1rem; color: #64748b; font-size: 0.875rem;">
                        Aktiv müraciətiniz yoxdur.
                    </div>
                @else
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        @foreach($myBookings as $booking)
                            <div style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 10px; padding: 1rem;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.4rem;">
                                    <span style="font-weight: 700; color: #fff; font-size: 0.875rem;">{{ $booking->slot->teacher->name }}</span>
                                    @if($booking->status == 'pending')
                                        <span style="color: #facc15; font-size: 0.75rem; font-weight: 600;">Gözləmədə</span>
                                    @elseif($booking->status == 'confirmed')
                                        <span style="color: #60a5fa; font-size: 0.75rem; font-weight: 600;">Təsdiqləndi</span>
                                    @elseif($booking->status == 'completed')
                                        <span style="color: #34d399; font-size: 0.75rem; font-weight: 600;">Tamamlandı</span>
                                    @endif
                                </div>
                                <div style="font-size: 0.8rem; color: #94a3b8;">
                                    📅 {{ \Carbon\Carbon::parse($booking->booking_date)->format('d.m.Y') }} ({{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }})
                                </div>
                                <div style="font-size: 0.8rem; color: #cbd5e1; margin-top: 0.4rem; background: rgba(255,255,255,0.03); padding: 0.4rem; border-radius: 6px;">
                                    "{{ $booking->topic }}"
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- Booking Modal --}}
    <div id="bookingModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center;">
        <div class="glass-panel" style="width: 100%; max-width: 480px; padding: 2rem; border-radius: 20px; background: #0f172a; border: 1px solid rgba(255,255,255,0.15);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.2rem; font-weight: 700; color: #fff; margin: 0;">✍️ Görüş Tarixi və Mövzu</h3>
                <button onclick="document.getElementById('bookingModal').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.25rem; cursor: pointer;">✕</button>
            </div>

            <form action="{{ route('portal.student.office-hours.book') }}" method="POST">
                @csrf
                <input type="hidden" name="slot_id" id="modalSlotId">

                <div style="margin-bottom: 1rem; padding: 0.75rem; background: rgba(59, 130, 246, 0.1); border-radius: 10px; color: #93c5fd; font-size: 0.875rem;">
                    Müəllim: <strong id="modalTeacherName" style="color: #fff;"></strong><br>
                    Qəbul günü: <span id="modalDayOfWeek" style="color: #fff;"></span> (<span id="modalStartTime"></span>)
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; color: #94a3b8; margin-bottom: 0.4rem;">Görüş Tarixi Seçin</label>
                    <input type="date" name="booking_date" min="{{ date('Y-m-d') }}" required style="width: 100%; background: #1e293b; border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 0.6rem 0.8rem; border-radius: 8px;">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.85rem; color: #94a3b8; margin-bottom: 0.4rem;">Müzakirə Olunacaq Mövzu / Sualınız</label>
                    <textarea name="topic" rows="3" required placeholder="Məs: Aralıq imtahanı mövzuları haqqında sualım var..." style="width: 100%; background: #1e293b; border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 0.6rem 0.8rem; border-radius: 8px; font-family: inherit;"></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" onclick="document.getElementById('bookingModal').style.display='none'" class="btn" style="background: rgba(255,255,255,0.1); color: #fff;">Ləğv et</button>
                    <button type="submit" class="btn btn-primary" style="background: #3b82f6;">Müraciət et</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openBookingModal(slotId, teacherName, dayOfWeek, startTime) {
            document.getElementById('modalSlotId').value = slotId;
            document.getElementById('modalTeacherName').innerText = teacherName;
            document.getElementById('modalDayOfWeek').innerText = dayOfWeek;
            document.getElementById('modalStartTime').innerText = startTime;
            document.getElementById('bookingModal').style.display = 'flex';
        }
    </script>

@endsection
