@extends('layouts.app')

@section('title', 'Onlayn Qeydiyyat — Xəzər Universiteti')

@section('content')

    <div class="container" style="padding: 4rem 1.5rem; max-width: 600px;">
        
        <div class="glass-panel" style="padding: 2.75rem; border-color: rgba(99, 102, 241, 0.35);">
            <div style="text-align: center; margin-bottom: 2rem;">
                <div style="width: 52px; height: 52px; background: linear-gradient(135deg, #002B49, #00A3E0); border-radius: 0.85rem; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 1rem;">
                    <i class="fa-solid fa-user-plus text-white"></i>
                </div>
                <h2 class="font-heading" style="font-size: 1.8rem; margin-bottom: 0.4rem;">Yeni Hesab Yarat</h2>
                <p style="color: #94a3b8; font-size: 0.9rem;">Xəzər Universitetinin elektron portalına qeydiyyat</p>
            </div>

            @if($errors->any())
                <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #fca5a5; padding: 0.75rem 1rem; border-radius: 0.65rem; margin-bottom: 1.5rem; font-size: 0.85rem;">
                    <ul style="margin: 0; padding-left: 1.25rem;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.submit') }}" method="POST">
                @csrf

                <div style="margin-bottom: 1.25rem;">
                    <label style="font-size: 0.85rem; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 0.4rem;">Ad və Soyadınız *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Məs: Kənan Qasımov" class="form-input">
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label style="font-size: 0.85rem; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 0.4rem;">Elektron Poçt (Email) *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="ad.soyad@example.com" class="form-input">
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label style="font-size: 0.85rem; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 0.4rem;">Əlaqə Nömrəsi *</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="+994 50 123 45 67" class="form-input">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.75rem;">
                    <div>
                        <label style="font-size: 0.85rem; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 0.4rem;">Şifrə *</label>
                        <input type="password" name="password" required placeholder="••••••••" class="form-input">
                    </div>
                    <div>
                        <label style="font-size: 0.85rem; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 0.4rem;">Şifrə Təkrarı *</label>
                        <input type="password" name="password_confirmation" required placeholder="••••••••" class="form-input">
                    </div>
                </div>

                <button type="submit" class="btn gradient-btn" style="width: 100%; padding: 0.85rem; font-size: 1rem; margin-bottom: 1.25rem;">
                    <i class="fa-solid fa-check-circle"></i> Qeydiyyatı Tamamla
                </button>

                <div style="text-align: center; font-size: 0.9rem; color: #94a3b8;">
                    Artıq hesabınız var? <a href="{{ route('login') }}" style="color: #818cf8; text-decoration: none; font-weight: 600;">Daxil olun</a>
                </div>
            </form>
        </div>

    </div>

@endsection
