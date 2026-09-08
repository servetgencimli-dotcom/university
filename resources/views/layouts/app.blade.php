<!DOCTYPE html>
<html lang="az" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Xəzər Universiteti — Tələbə & Akademik Portalı')</title>
    <meta name="description" content="Xəzər Universiteti — Azərbaycanda ilk beynəlxalq kredit sistemli ali təhsil və tədqiqat ocağı (Təsis: 1991).">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Custom Modern Styling -->
    <style>
        :root {
            --primary: #00A3E0;
            --primary-hover: #0284c7;
            --khazar-navy: #002B49;
            --khazar-gold: #C89D35;
            --dark-bg: #070f1e;
            --dark-card: #0c1829;
            --dark-border: rgba(255, 255, 255, 0.08);
            --text-main: #f3f4f6;
            --text-muted: #94a3b8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--dark-bg);
            color: var(--text-main);
            overflow-x: hidden;
            line-height: 1.6;
        }

        h1, h2, h3, h4, h5, h6, .font-heading {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }

        /* Glassmorphism & Cards */
        .glass-panel {
            background: rgba(12, 24, 41, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--dark-border);
            border-radius: 1rem;
        }

        .glass-card {
            background: rgba(17, 34, 58, 0.55);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-card:hover {
            transform: translateY(-4px);
            border-color: rgba(0, 163, 224, 0.4);
            box-shadow: 0 12px 30px -10px rgba(0, 163, 224, 0.3);
        }

        /* Gradients */
        .gradient-text {
            background: linear-gradient(135deg, #7dd3fc 0%, #00A3E0 50%, #38bdf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .gradient-gold {
            background: linear-gradient(135deg, #fde047 0%, #C89D35 50%, #a16207 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .gradient-btn {
            background: linear-gradient(135deg, #00A3E0 0%, #0284c7 100%);
            color: #fff;
            box-shadow: 0 4px 20px -2px rgba(0, 163, 224, 0.5);
            transition: all 0.3s ease;
        }

        .gradient-btn:hover {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            box-shadow: 0 8px 25px -2px rgba(0, 163, 224, 0.7);
            transform: translateY(-2px);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-size: 0.95rem;
            font-weight: 600;
            border-radius: 0.75rem;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #fff;
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        /* Container */
        .container {
            max-width: 1280px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }

        /* Custom Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-radius: 9999px;
        }

        .badge-primary { background: rgba(0, 163, 224, 0.15); color: #38bdf8; border: 1px solid rgba(0, 163, 224, 0.3); }
        .badge-accent { background: rgba(200, 157, 53, 0.15); color: #fbbf24; border: 1px solid rgba(200, 157, 53, 0.3); }
        .badge-success { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
        .badge-warning { background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }
        .badge-danger { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }

        /* Form Controls */
        .form-input, .form-select, .form-textarea {
            width: 100%;
            background: rgba(7, 15, 30, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #fff;
            padding: 0.75rem 1rem;
            border-radius: 0.65rem;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: #00A3E0;
            box-shadow: 0 0 0 3px rgba(0, 163, 224, 0.25);
            background: rgba(7, 15, 30, 0.95);
        }

        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #070f1e; }
        ::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #334155; }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between">

    <!-- Top Announcement Bar -->
    <div style="background: linear-gradient(90deg, #001a2e, #002B49, #001a2e); border-bottom: 1px solid rgba(0, 163, 224, 0.2); font-size: 0.85rem; padding: 0.4rem 0;">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <span class="badge badge-accent"><i class="fa-solid fa-graduation-cap"></i> 2026/2027 Qəbulu Açıqdır</span>
                <span style="color: #cbd5e1;">Xəzər Universitetinin Bakalavr və Magistratura proqramlarına qeydiyyat başlayıb!</span>
            </div>
            <div style="display: flex; align-items: center; gap: 1.25rem;">
                <a href="{{ route('admissions.track') }}" style="color: #7dd3fc; text-decoration: none; display: flex; align-items: center; gap: 0.35rem;">
                    <i class="fa-solid fa-magnifying-glass-location"></i> Müraciət Statusunu Yoxla
                </a>
                <span style="color: rgba(255,255,255,0.2);">|</span>
                <div style="color: #94a3b8; display: flex; gap: 0.75rem;">
                    <span style="color: #38bdf8; font-weight: 600;">AZ</span>
                    <span style="cursor: pointer;">EN</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <nav class="glass-panel" style="position: sticky; top: 0; z-index: 50; border-radius: 0; border-top: none; border-left: none; border-right: none;">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center; padding-top: 0.85rem; padding-bottom: 0.85rem;">
            
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" style="display: flex; align-items: center; gap: 0.85rem; text-decoration: none; color: #fff;">
                <div style="width: 44px; height: 44px; background: linear-gradient(135deg, #002B49, #00A3E0); border: 2px solid #C89D35; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; box-shadow: 0 4px 15px rgba(0, 163, 224, 0.4);">
                    <i class="fa-solid fa-building-columns text-white"></i>
                </div>
                <div>
                    <div class="font-heading" style="font-size: 1.3rem; line-height: 1.1; letter-spacing: -0.02em;">XƏZƏR <span class="gradient-text">UNIVERSİTETİ</span></div>
                    <div style="font-size: 0.7rem; color: #94a3b8; letter-spacing: 0.08em; text-transform: uppercase;">Khazar University • Est. 1991</div>
                </div>
            </a>

            <!-- Navigation Links -->
            <div style="display: flex; align-items: center; gap: 1.8rem; font-weight: 500; font-size: 0.95rem;">
                <a href="{{ route('programs.index') }}" style="color: #e2e8f0; text-decoration: none; display: flex; align-items: center; gap: 0.35rem; transition: color 0.2s;" onmouseover="this.style.color='#38bdf8'" onmouseout="this.style.color='#e2e8f0'">
                    <i class="fa-solid fa-book-open" style="font-size: 0.85rem; opacity: 0.7;"></i> İxtisaslar
                </a>
                <a href="{{ route('faculties.index') }}" style="color: #e2e8f0; text-decoration: none; display: flex; align-items: center; gap: 0.35rem; transition: color 0.2s;" onmouseover="this.style.color='#38bdf8'" onmouseout="this.style.color='#e2e8f0'">
                    <i class="fa-solid fa-building-columns" style="font-size: 0.85rem; opacity: 0.7;"></i> Məktəblər
                </a>
                <a href="{{ route('campus.map') }}" style="color: #e2e8f0; text-decoration: none; display: flex; align-items: center; gap: 0.35rem; transition: color 0.2s;" onmouseover="this.style.color='#38bdf8'" onmouseout="this.style.color='#e2e8f0'">
                    <i class="fa-solid fa-map-location-dot" style="font-size: 0.85rem; opacity: 0.7;"></i> Kampuslar
                </a>
                <a href="{{ route('research.index') }}" style="color: #e2e8f0; text-decoration: none; display: flex; align-items: center; gap: 0.35rem; transition: color 0.2s;" onmouseover="this.style.color='#38bdf8'" onmouseout="this.style.color='#e2e8f0'">
                    <i class="fa-solid fa-atom" style="font-size: 0.85rem; opacity: 0.7;"></i> Elmi Tədqiqat
                </a>
                <a href="{{ route('events.index') }}" style="color: #e2e8f0; text-decoration: none; display: flex; align-items: center; gap: 0.35rem; transition: color 0.2s;" onmouseover="this.style.color='#38bdf8'" onmouseout="this.style.color='#e2e8f0'">
                    <i class="fa-solid fa-calendar-days" style="font-size: 0.85rem; opacity: 0.7;"></i> Tədbirlər
                </a>
                <a href="{{ route('news.index') }}" style="color: #e2e8f0; text-decoration: none; display: flex; align-items: center; gap: 0.35rem; transition: color 0.2s;" onmouseover="this.style.color='#38bdf8'" onmouseout="this.style.color='#e2e8f0'">
                    <i class="fa-solid fa-newspaper" style="font-size: 0.85rem; opacity: 0.7;"></i> Xəbərlər
                </a>
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; align-items: center; gap: 0.85rem;">
                <a href="{{ route('admissions.apply') }}" class="btn gradient-btn" style="padding: 0.6rem 1.25rem;">
                    <i class="fa-solid fa-paper-plane"></i> Müraciət Et
                </a>

                @auth
                    <a href="{{ route('portal.dashboard') }}" class="btn btn-outline" style="padding: 0.6rem 1.1rem; border-color: #00A3E0; color: #7dd3fc;">
                        <i class="fa-solid fa-gauge-high"></i> {{ Auth::user()->name }}
                    </a>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-outline" style="padding: 0.6rem 0.8rem;" title="Çıxış">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline" style="padding: 0.6rem 1.1rem;">
                        <i class="fa-solid fa-user-lock"></i> Tələbə / Portal Girişi
                    </a>
                @endauth
            </div>

        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div style="background: rgba(16, 185, 129, 0.2); border-bottom: 1px solid rgba(16, 185, 129, 0.4); color: #6ee7b7; padding: 0.85rem; text-align: center; font-weight: 600;">
            <div class="container" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div style="background: rgba(239, 68, 68, 0.2); border-bottom: 1px solid rgba(239, 68, 68, 0.4); color: #fca5a5; padding: 0.85rem; text-align: center; font-weight: 600;">
            <div class="container" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Page Content -->
    <main style="flex: 1 0 auto;">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer style="background: #030812; border-top: 1px solid rgba(255, 255, 255, 0.08); margin-top: 5rem; padding-top: 4rem; padding-bottom: 2rem;">
        <div class="container">
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1.5fr; gap: 3rem; margin-bottom: 3.5rem;">
                
                <!-- Col 1 -->
                <div>
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #002B49, #00A3E0); border: 1.5px solid #C89D35; border-radius: 0.6rem; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                            <i class="fa-solid fa-building-columns text-white"></i>
                        </div>
                        <span class="font-heading" style="font-size: 1.3rem; letter-spacing: -0.02em;">XƏZƏR UNIVERSİTETİ</span>
                    </div>
                    <p style="color: #94a3b8; font-size: 0.95rem; line-height: 1.7; margin-bottom: 1.5rem;">
                        1991-ci ildən bəri Azərbaycanda Qərb təhsil modeli və Amerika kredit sistemini uğurla tətbiq edən ilk müstəqil beynəlxalq ali məktəb.
                    </p>
                    <div style="display: flex; gap: 0.75rem;">
                        <a href="https://facebook.com" target="_blank" style="width: 36px; height: 36px; background: rgba(255,255,255,0.05); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #cbd5e1; text-decoration: none;"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="https://linkedin.com" target="_blank" style="width: 36px; height: 36px; background: rgba(255,255,255,0.05); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #cbd5e1; text-decoration: none;"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="https://instagram.com" target="_blank" style="width: 36px; height: 36px; background: rgba(255,255,255,0.05); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #cbd5e1; text-decoration: none;"><i class="fa-brands fa-instagram"></i></a>
                        <a href="https://youtube.com" target="_blank" style="width: 36px; height: 36px; background: rgba(255,255,255,0.05); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #cbd5e1; text-decoration: none;"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>

                <!-- Col 2 -->
                <div>
                    <h4 class="font-heading" style="font-size: 1.1rem; margin-bottom: 1.25rem; color: #f8fafc;">Akademik Məktəblər</h4>
                    <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.7rem; font-size: 0.9rem;">
                        <li><a href="{{ route('faculties.index') }}" style="color: #94a3b8; text-decoration: none;">Təbiət Elmləri və Mühəndislik</a></li>
                        <li><a href="{{ route('faculties.index') }}" style="color: #94a3b8; text-decoration: none;">İqtisadiyyat və Menecment</a></li>
                        <li><a href="{{ route('faculties.index') }}" style="color: #94a3b8; text-decoration: none;">Humanitar, Təhsil və Sosial Elmlər</a></li>
                        <li><a href="{{ route('faculties.index') }}" style="color: #94a3b8; text-decoration: none;">Hasvet Baytarlıq Təbabəti</a></li>
                        <li><a href="{{ route('programs.index') }}" style="color: #94a3b8; text-decoration: none;">Bakalavr & Magistr İxtisasları</a></li>
                    </ul>
                </div>

                <!-- Col 3 -->
                <div>
                    <h4 class="font-heading" style="font-size: 1.1rem; margin-bottom: 1.25rem; color: #f8fafc;">Qəbul & Tələbə</h4>
                    <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.7rem; font-size: 0.9rem;">
                        <li><a href="{{ route('admissions.apply') }}" style="color: #94a3b8; text-decoration: none;">Elektron Sənəd Qəbulu</a></li>
                        <li><a href="{{ route('admissions.track') }}" style="color: #94a3b8; text-decoration: none;">Müraciət İzləmə Sistemi</a></li>
                        <li><a href="{{ route('events.index') }}" style="color: #94a3b8; text-decoration: none;">Açıq Qapı Günləri & Simpozium</a></li>
                        <li><a href="{{ route('campus.map') }}" style="color: #94a3b8; text-decoration: none;">Kitabxana və İnformasiya Mərkəzi (LIC)</a></li>
                        <li><a href="{{ route('login') }}" style="color: #94a3b8; text-decoration: none;">Tələbə Şəxsi Kabineti</a></li>
                    </ul>
                </div>

                <!-- Col 4 -->
                <div>
                    <h4 class="font-heading" style="font-size: 1.1rem; margin-bottom: 1.25rem; color: #f8fafc;">Əlaqə & Kampuslar</h4>
                    <div style="display: flex; flex-direction: column; gap: 0.9rem; font-size: 0.9rem; color: #94a3b8;">
                        <div style="display: flex; gap: 0.65rem;">
                            <i class="fa-solid fa-location-dot" style="color: #00A3E0; margin-top: 0.2rem;"></i>
                            <span>Neftçilər Korpusu: Məhsəti Gəncəvi küç. 41, Bakı, AZ1096</span>
                        </div>
                        <div style="display: flex; gap: 0.65rem;">
                            <i class="fa-solid fa-location-dot" style="color: #00A3E0; margin-top: 0.2rem;"></i>
                            <span>Şəhər Korpusu: Bəşir Səfəroğlu küç. 122, Bakı</span>
                        </div>
                        <div style="display: flex; gap: 0.65rem;">
                            <i class="fa-solid fa-phone" style="color: #00A3E0; margin-top: 0.2rem;"></i>
                            <span>+994 (12) 421 10 93 / +994 (12) 421 79 16</span>
                        </div>
                        <div style="display: flex; gap: 0.65rem;">
                            <i class="fa-solid fa-envelope" style="color: #00A3E0; margin-top: 0.2rem;"></i>
                            <span>contact@khazar.org | admissions@khazar.org</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Bottom Subfooter -->
            <div style="border-top: 1px solid rgba(255, 255, 255, 0.05); padding-top: 1.5rem; display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; color: #64748b;">
                <div>
                    © {{ date('Y') }} Xəzər Universiteti (Khazar University). Bütün hüquqlar qorunur.
                </div>
                <div style="display: flex; gap: 1.5rem;">
                    <a href="https://khazar.org" target="_blank" style="color: #64748b; text-decoration: none;">khazar.org</a>
                    <a href="{{ route('login') }}" style="color: #00A3E0; text-decoration: none;"><i class="fa-solid fa-lock"></i> Vahid Giriş Portalı</a>
                </div>
            </div>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
