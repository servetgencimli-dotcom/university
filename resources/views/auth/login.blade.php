@extends('layouts.app')

@section('title', 'Vahid Giriş Portalı (SSO) — Xəzər Universiteti')

@section('content')

    <div class="container" style="padding: 4.5rem 1.5rem; max-width: 520px;">
        
        <!-- Standard Secure Login Form -->
        <div class="glass-panel" style="padding: 2.75rem; border-color: rgba(0, 163, 224, 0.35);">
            <div style="text-align: center; margin-bottom: 2rem;">
                <div style="width: 52px; height: 52px; background: linear-gradient(135deg, #002B49, #00A3E0); border-radius: 0.85rem; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 1rem; box-shadow: 0 4px 15px rgba(0, 163, 224, 0.3);">
                    <i class="fa-solid fa-lock text-white"></i>
                </div>
                <h2 class="font-heading" style="font-size: 1.8rem; margin-bottom: 0.4rem;">Vahid Giriş Portalı</h2>
                <p style="color: #94a3b8; font-size: 0.9rem;">Xəzər Universiteti e-poçt və şifrənizlə daxil olun</p>
            </div>

            @if($errors->any())
                <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #fca5a5; padding: 0.75rem 1rem; border-radius: 0.65rem; margin-bottom: 1.5rem; font-size: 0.85rem;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST">
                @csrf

                <div style="margin-bottom: 1.25rem;">
                    <label style="font-size: 0.85rem; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 0.4rem;">E-poçt Ünvanı</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="ad.soyad@khazar.org" class="form-input">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.4rem;">
                        <label style="font-size: 0.85rem; font-weight: 600; color: #cbd5e1;">Şifrə</label>
                    </div>
                    <input type="password" name="password" required placeholder="••••••••" class="form-input">
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="checkbox" name="remember" id="remember" style="accent-color: #00A3E0;">
                        <label for="remember" style="font-size: 0.85rem; color: #94a3b8; cursor: pointer;">Məni xatırla</label>
                    </div>
                    <a href="{{ route('admissions.apply') }}" style="font-size: 0.85rem; color: #38bdf8; text-decoration: none;">Onlayn Qəbul</a>
                </div>

                <button type="submit" class="btn gradient-btn" style="width: 100%; padding: 0.85rem; font-size: 1rem; margin-bottom: 1.5rem;">
                    <i class="fa-solid fa-right-to-bracket"></i> Sistemə Daxil Ol
                </button>

                <div style="text-align: center; font-size: 0.9rem; color: #94a3b8; border-top: 1px solid rgba(255,255,255,0.06); padding-top: 1.25rem;">
                    Hesabınız yoxdur? <a href="{{ route('register') }}" style="color: #38bdf8; text-decoration: none; font-weight: 600;">Qeydiyyatdan keçin</a>
                </div>
            </form>
        </div>

    </div>

@endsection
