@extends('layouts.portal')

@section('title', 'QR Davamiyyət Qeydiyyatı — Xəzər Universiteti')
@section('header_title', 'QR Dərs Qeydiyyatı')

@section('content')

    <div style="max-width: 500px; margin: 2rem auto;">
        
        @if(!empty($expired))
            <div class="glass-panel" style="padding: 2.5rem 1.5rem; text-align: center; border-radius: 20px; border-left: 4px solid #ef4444; background: rgba(239, 68, 68, 0.08);">
                <div style="font-size: 3rem; margin-bottom: 1rem;">⏰</div>
                <h2 style="font-size: 1.35rem; font-weight: 700; color: #fca5a5; margin-bottom: 0.75rem;">Sessiyanın Vaxtı Bitib</h2>
                <p style="color: #cbd5e1; font-size: 0.925rem; line-height: 1.5; margin-bottom: 1.5rem;">
                    {{ $message ?? 'QR kodun istifadə vaxtı başa çatıb. Lütfən auditoriyadakı ekrandan yeni QR kodu skan edin.' }}
                </p>
                <a href="{{ route('portal.dashboard') }}" class="btn btn-primary" style="background: rgba(255,255,255,0.1); color: #fff;">
                    Portala Qayıt
                </a>
            </div>
        @elseif(!empty($error))
            <div class="glass-panel" style="padding: 2.5rem 1.5rem; text-align: center; border-radius: 20px; border-left: 4px solid #f59e0b; background: rgba(245, 158, 11, 0.08);">
                <div style="font-size: 3rem; margin-bottom: 1rem;">⚠️</div>
                <h2 style="font-size: 1.35rem; font-weight: 700; color: #fcd34d; margin-bottom: 0.75rem;">Xəbərdarlıq</h2>
                <p style="color: #cbd5e1; font-size: 0.925rem; line-height: 1.5; margin-bottom: 1.5rem;">
                    {{ $error }}
                </p>
                <a href="{{ route('portal.dashboard') }}" class="btn btn-primary" style="background: rgba(255,255,255,0.1); color: #fff;">
                    Portala Qayıt
                </a>
            </div>
        @else
            <div class="glass-panel" style="padding: 2.5rem 2rem; border-radius: 24px; text-align: center; background: linear-gradient(135deg, rgba(30, 41, 59, 0.8), rgba(15, 23, 42, 0.9)); border: 1px solid rgba(255,255,255,0.12);">
                
                @if(session('success') || !empty($alreadyPresent))
                    <div style="width: 70px; height: 70px; border-radius: 50%; background: linear-gradient(135deg, #10b981, #059669); color: #fff; font-size: 2.25rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem; box-shadow: 0 0 30px rgba(16, 185, 129, 0.4);">
                        ✓
                    </div>
                    <h2 style="font-size: 1.4rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;">İştirakınız Qeydə Alındı!</h2>
                    <p style="color: #94a3b8; font-size: 0.9rem; margin-bottom: 1.5rem;">
                        Bu dərs üçün davamiyyətiniz canlı olaraq təsdiqləndi.
                    </p>
                @else
                    <div style="width: 70px; height: 70px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: #fff; font-size: 2.25rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem; box-shadow: 0 0 30px rgba(59, 130, 246, 0.4);">
                        📲
                    </div>
                    <h2 style="font-size: 1.4rem; font-weight: 700; color: #fff; margin-bottom: 0.5rem;">QR Davamiyyəti Təsdiqləyin</h2>
                    <p style="color: #94a3b8; font-size: 0.9rem; margin-bottom: 1.5rem;">
                        Aşağıdakı dərsin canlı iştirakçısı olduğunuzu təsdiq edin.
                    </p>
                @endif

                <div style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 1.25rem; text-align: left; margin-bottom: 1.75rem;">
                    <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #38bdf8; font-weight: 700; margin-bottom: 0.25rem;">
                        Fənnvə Qrup
                    </div>
                    <div style="font-weight: 700; color: #fff; font-size: 1.1rem; margin-bottom: 0.5rem;">
                        {{ $session->courseSection->course->name }} ({{ $session->courseSection->section_code }})
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; font-size: 0.825rem; color: #cbd5e1; border-top: 1px dashed rgba(255,255,255,0.08); padding-top: 0.75rem; margin-top: 0.5rem;">
                        <div>
                            <span style="color: #64748b;">Müəllim:</span><br>
                            <strong style="color: #f8fafc;">{{ $session->teacher->name ?? 'Fənn Müəllimi' }}</strong>
                        </div>
                        <div>
                            <span style="color: #64748b;">Tarix:</span><br>
                            <strong style="color: #f8fafc;">{{ \Carbon\Carbon::parse($session->session_date)->format('d.m.Y') }}</strong>
                        </div>
                    </div>
                </div>

                @if(empty($alreadyPresent) && !session('success'))
                    <form action="{{ route('portal.qr.process', $session->session_code) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem; border-radius: 12px; font-weight: 700; font-size: 1rem; background: linear-gradient(135deg, #10b981, #059669); border: none; cursor: pointer; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.4);">
                            🟢 Davamiyyəti Qeydə Al
                        </button>
                    </form>
                @else
                    <a href="{{ route('portal.dashboard') }}" class="btn" style="display: block; width: 100%; padding: 0.75rem; border-radius: 12px; font-weight: 600; background: rgba(255,255,255,0.08); color: #fff; text-decoration: none;">
                        Portala Qayıt
                    </a>
                @endif

            </div>
        @endif

    </div>

@endsection
