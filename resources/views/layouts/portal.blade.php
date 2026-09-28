<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('portal_title', 'Tələbə & Akademik Portalı — Xəzər Universiteti')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS (via CDN for rich styling in portal sub-views) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            blue: '#00A3E0',
                            navy: '#002B49',
                            gold: '#C89D35'
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        :root {
            --primary: #00A3E0;
            --primary-dark: #0284c7;
            --khazar-navy: #002B49;
            --khazar-gold: #C89D35;
            --sidebar-bg: #061124;
            --main-bg: #040a17;
            --card-bg: rgba(12, 24, 44, 0.8);
            --border-color: rgba(255, 255, 255, 0.08);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background-color: var(--main-bg); color: var(--text-main); display: flex; min-height: 100vh; }

        h1, h2, h3, h4, h5, h6, .font-heading { font-family: 'Outfit', sans-serif; font-weight: 700; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        /* Glass Panel */
        .glass-panel {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
        }

        .glass-card {
            background: rgba(17, 34, 58, 0.55);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 0.85rem;
            transition: all 0.2s ease;
        }

        .glass-card:hover {
            border-color: rgba(0, 163, 224, 0.35);
        }

        /* Sidebar Styling */
        .portal-sidebar {
            width: 280px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 40;
            padding: 1.5rem 1.25rem;
            overflow-y: auto;
        }

        .portal-content {
            margin-left: 280px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: #f8fafc;
            color: #0f172a;
        }

        /* Nav links */
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.75rem 1rem;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 500;
            border-radius: 0.65rem;
            transition: all 0.2s ease;
            margin-bottom: 0.25rem;
        }

        .nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.05);
        }

        .nav-link.active {
            color: #fff;
            background: linear-gradient(90deg, rgba(0, 163, 224, 0.25), rgba(0, 163, 224, 0.08));
            border-left: 3px solid #00A3E0;
            font-weight: 600;
        }

        .nav-link.active i {
            color: #38bdf8;
        }

        /* Buttons & Badges */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.65rem 1.25rem;
            font-size: 0.9rem;
            font-weight: 600;
            border-radius: 0.65rem;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #00A3E0, #0284c7);
            color: #fff;
            box-shadow: 0 4px 15px rgba(0, 163, 224, 0.35);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0284c7, #0369a1);
        }

        .btn-outline {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #e2e8f0;
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.2rem 0.65rem;
            font-size: 0.75rem;
            font-weight: 700;
            border-radius: 9999px;
            letter-spacing: 0.03em;
        }

        .badge-system { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }
        .badge-admin { background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }
        .badge-teacher { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
        .badge-master { background: rgba(168, 85, 247, 0.15); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.3); }
        .badge-student { background: rgba(0, 163, 224, 0.15); color: #38bdf8; border: 1px solid rgba(0, 163, 224, 0.3); }
        .badge-applicant { background: rgba(200, 157, 53, 0.15); color: #fbbf24; border: 1px solid rgba(200, 157, 53, 0.3); }
    </style>
</head>
<body>

    @if(session()->has('impersonator_id'))
        <div style="background: linear-gradient(90deg, #f59e0b, #d97706); color: #0f172a; padding: 0.65rem 2rem; font-weight: 700; display: flex; justify-content: space-between; align-items: center; z-index: 999; position: fixed; top: 0; left: 280px; right: 0; font-size: 0.88rem; box-shadow: 0 2px 10px rgba(0,0,0,0.3);">
            <div style="display: flex; align-items: center; gap: 0.6rem;">
                <i class="fa-solid fa-user-secret" style="font-size: 1.1rem;"></i>
                <span>Siz hazırda <strong>{{ Auth::user()->name }}</strong> ({{ Auth::user()->role_title }}) profilindəsiniz.</span>
            </div>
            <form action="{{ route('portal.admin.stop-impersonating') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="btn" style="background: #0f172a; color: #fff; padding: 0.35rem 0.95rem; font-size: 0.8rem; border-radius: 0.45rem; border: 1px solid rgba(255,255,255,0.2); cursor: pointer; font-weight: 600;">
                    <i class="fa-solid fa-arrow-left"></i> Super Admin Hesabına Qayıt
                </button>
            </form>
        </div>
    @endif

    <!-- Portal Sidebar -->
    <aside class="portal-sidebar">
        <div>
            <!-- Brand -->
            <a href="{{ route('home') }}" style="display: flex; align-items: center; gap: 0.75rem; text-decoration: none; color: #fff; margin-bottom: 1.5rem;">
                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #002B49, #00A3E0); border: 1.5px solid #C89D35; border-radius: 0.65rem; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                    <i class="fa-solid fa-building-columns text-white"></i>
                </div>
                <div>
                    <div class="font-heading" style="font-size: 1.15rem; line-height: 1.1;">XƏZƏR <span style="color: #38bdf8;">PORTAL</span></div>
                    <div style="font-size: 0.65rem; color: #94a3b8; text-transform: uppercase;">Khazar University OS</div>
                </div>
            </a>

            <!-- Active User Quick Summary -->
            <div class="glass-card" style="padding: 0.85rem; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 40px; height: 40px; background: #0c1829; border: 1px solid rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; color: #38bdf8; flex-shrink: 0;">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
                <div style="overflow: hidden;">
                    <div style="font-size: 0.88rem; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ Auth::user()->name }}</div>
                    <div style="font-size: 0.75rem;">
                        @if(Auth::user()->role === 'system_admin')
                            <span class="badge badge-system"><i class="fa-solid fa-shield-halved"></i> Super Admin</span>
                        @elseif(Auth::user()->role === 'admin')
                            <span class="badge badge-admin"><i class="fa-solid fa-user-gear"></i> Məktəb Admini</span>
                        @elseif(Auth::user()->role === 'teacher')
                            <span class="badge badge-teacher"><i class="fa-solid fa-chalkboard-user"></i> Professor</span>
                        @elseif(Auth::user()->role === 'master_student')
                            <span class="badge badge-master"><i class="fa-solid fa-user-graduate"></i> Magistrant</span>
                        @elseif(Auth::user()->role === 'student')
                            <span class="badge badge-student"><i class="fa-solid fa-graduation-cap"></i> Bakalavr</span>
                        @else
                            <span class="badge badge-applicant"><i class="fa-solid fa-id-badge"></i> Abituriyent</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <div style="margin-bottom: 1.5rem;">
                <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.08em; margin-bottom: 0.4rem; padding-left: 0.5rem;">Əsas Bölmələr</div>
                
                <a href="{{ route('portal.dashboard') }}" class="nav-link {{ request()->routeIs('portal.dashboard') && !request()->filled('view_as') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge-high"></i> İdarəetmə Paneli
                </a>

                <a href="{{ route('portal.notifications.index') }}" class="nav-link {{ request()->routeIs('portal.notifications.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-bell"></i> Bildirişlər
                    @if(Auth::user()->unreadNotificationsCount() > 0)
                        <span class="ml-auto px-2 py-0.5 text-xs font-bold bg-blue-600 text-white rounded-full">{{ Auth::user()->unreadNotificationsCount() }}</span>
                    @endif
                </a>

                <!-- Student Nav -->
                @if(Auth::user()->isStudent())
                    <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #38bdf8; letter-spacing: 0.08em; margin-top: 1rem; margin-bottom: 0.4rem; padding-left: 0.5rem;">Tələbə Portalı</div>
                    
                    <a href="{{ route('portal.schedule') }}" class="nav-link {{ request()->routeIs('portal.schedule*') ? 'active' : '' }}">
                        <i class="fa-solid fa-table-cells text-sky-400"></i> Dərs cədvəli
                    </a>
                    
                    <a href="{{ route('portal.semester-grades') }}" class="nav-link {{ request()->routeIs('portal.semester-grades*') ? 'active' : '' }}">
                        <i class="fa-solid fa-arrow-down-wide-short text-emerald-400"></i> Semestr qiymətləri
                    </a>

                    <a href="{{ route('portal.appeals.index', ['type' => 'makeup_exam']) }}" class="nav-link {{ request()->routeIs('portal.appeals*') && request('type') === 'makeup_exam' ? 'active' : '' }}">
                        <i class="fa-solid fa-user-pen text-amber-400"></i> Tamamlama imtahanı
                    </a>

                    <a href="{{ route('portal.appeals.index', ['type' => 'grade_objection']) }}" class="nav-link {{ request()->routeIs('portal.appeals*') && request('type') === 'grade_objection' ? 'active' : '' }}">
                        <i class="fa-solid fa-scale-balanced text-rose-400"></i> İmtahana etiraz et
                    </a>

                    <a href="{{ route('portal.appeals.index', ['type' => 'penalty_exam']) }}" class="nav-link {{ request()->routeIs('portal.appeals*') && request('type') === 'penalty_exam' ? 'active' : '' }}">
                        <i class="fa-solid fa-gavel text-orange-400"></i> Cərimə imtahanı
                    </a>

                    <a href="{{ route('portal.appeals.index', ['type' => 'dean_petition']) }}" class="nav-link {{ request()->routeIs('portal.appeals*') && request('type') === 'dean_petition' ? 'active' : '' }}">
                        <i class="fa-solid fa-building-user text-indigo-400"></i> Dekanlığa müraciət
                    </a>

                    <a href="{{ route('portal.transcript') }}" class="nav-link {{ request()->routeIs('portal.transcript*') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-lines text-teal-400"></i> Transkript
                    </a>

                    <a href="{{ route('portal.finance.index') }}" class="nav-link {{ request()->routeIs('portal.finance.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-coins text-amber-300"></i> Ödəniş əməliyyatları
                        @if(Auth::user()->hasUnpaidDebt())
                            <span class="ml-auto px-1.5 py-0.5 text-[10px] font-bold bg-red-600 text-white rounded-full">BORC</span>
                        @endif
                    </a>

                    <a href="{{ route('portal.attendance-report') }}" class="nav-link {{ request()->routeIs('portal.attendance-report*') ? 'active' : '' }}">
                        <i class="fa-solid fa-people-group text-blue-400"></i> Kursun Davamı Məlumatı
                    </a>

                    <a href="{{ route('portal.registration.index') }}" class="nav-link {{ request()->routeIs('portal.registration.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-book-bookmark text-purple-400"></i> Fənn Qeydiyyatı
                    </a>
                @endif

                <!-- Teacher Nav -->
                @if(Auth::user()->isTeacher())
                    <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #34d399; letter-spacing: 0.08em; margin-top: 1rem; margin-bottom: 0.4rem; padding-left: 0.5rem;">Müəllim Nəzarəti</div>
                    
                    <a href="{{ route('portal.admin.appeals.index') }}" class="nav-link {{ request()->routeIs('portal.admin.appeals.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-inbox text-amber-400"></i> Tələbə Apellyasiyaları
                    </a>
                @endif

                <!-- Super Admin Nav -->
                @if(Auth::user()->isSystemAdmin() || Auth::user()->isAdmin())
                    <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #f87171; letter-spacing: 0.08em; margin-top: 1rem; margin-bottom: 0.4rem; padding-left: 0.5rem;">İdarəetmə & Qərarlar</div>
                    <a href="{{ route('portal.admin.appeals.index') }}" class="nav-link {{ request()->routeIs('portal.admin.appeals.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-envelope-open-text text-amber-400"></i> Bütün Müraciətlər
                    </a>
                @endif

                <!-- Super Admin Nav -->
                @if(Auth::user()->isSystemAdmin())
                    <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #f87171; letter-spacing: 0.08em; margin-top: 1rem; margin-bottom: 0.4rem; padding-left: 0.5rem;">Super Admin Nəzarəti</div>
                    <a href="{{ route('portal.admin.users.index') }}" class="nav-link {{ request()->routeIs('portal.admin.users.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-users-gear text-red-400"></i> İstifadəçilər & İcazələr
                    </a>
                    <a href="{{ route('portal.admin.dim.index') }}" class="nav-link {{ request()->routeIs('portal.admin.dim.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-signature text-amber-400"></i> DİM Qəbul Reyestri
                    </a>
                    <a href="{{ route('portal.admin.cms.index') }}" class="nav-link {{ request()->routeIs('portal.admin.cms.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-sliders text-indigo-400"></i> CMS & Semestrlər
                    </a>
                    <a href="{{ route('portal.admin.audit-logs') }}" class="nav-link {{ request()->routeIs('portal.admin.audit-logs*') ? 'active' : '' }}">
                        <i class="fa-solid fa-shield-halved text-emerald-400"></i> Audit & Təhlükəsizlik Jurnalı
                    </a>

                    <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #38bdf8; letter-spacing: 0.08em; margin-top: 1rem; margin-bottom: 0.4rem; padding-left: 0.5rem;">Portallara Canlı Baxış</div>
                    <a href="{{ route('portal.dashboard', ['view_as' => 'teacher']) }}" class="nav-link {{ request('view_as') === 'teacher' ? 'active' : '' }}">
                        <i class="fa-solid fa-chalkboard-user"></i> Müəllim Paneli
                    </a>
                    <a href="{{ route('portal.dashboard', ['view_as' => 'student']) }}" class="nav-link {{ request('view_as') === 'student' ? 'active' : '' }}">
                        <i class="fa-solid fa-graduation-cap"></i> Bakalavr Paneli
                    </a>
                    <a href="{{ route('portal.dashboard', ['view_as' => 'master_student']) }}" class="nav-link {{ request('view_as') === 'master_student' ? 'active' : '' }}">
                        <i class="fa-solid fa-user-graduate"></i> Magistrant Paneli
                    </a>
                    <a href="{{ route('portal.dashboard', ['view_as' => 'admin']) }}" class="nav-link {{ request('view_as') === 'admin' ? 'active' : '' }}">
                        <i class="fa-solid fa-user-gear"></i> Məktəb Dekan Paneli
                    </a>
                @endif
                
                <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.08em; margin-top: 1rem; margin-bottom: 0.4rem; padding-left: 0.5rem;">Sürətli Keçid</div>
                <a href="{{ route('home') }}" class="nav-link">
                    <i class="fa-solid fa-globe"></i> Əsas Veb Sayt
                </a>
                <a href="{{ route('campus.map') }}" class="nav-link">
                    <i class="fa-solid fa-map-location-dot"></i> Kampuslar
                </a>
            </div>
        </div>

        <!-- Logout Button -->
        <div class="pt-4 border-t border-slate-800">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline" style="width: 100%; justify-content: flex-start; color: #f87171; border-color: rgba(239, 68, 68, 0.2);">
                    <i class="fa-solid fa-right-from-bracket"></i> Sistemdən Çıxış
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="portal-content {{ session()->has('impersonator_id') ? 'pt-12' : '' }}">
        
        <!-- Topbar -->
        <header style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(12px); border-bottom: 1px solid #e2e8f0; padding: 1rem 2rem; position: sticky; top: {{ session()->has('impersonator_id') ? '48px' : '0' }}; z-index: 30; display: flex; justify-content: space-between; align-items: center; color: #0f172a;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <h1 class="font-heading" style="font-size: 1.25rem; font-weight: 700; color: #0f172a;">
                    @if(trim($__env->yieldContent('portal_title')))
                        @yield('portal_title')
                    @elseif(trim($__env->yieldContent('header_title')))
                        @yield('header_title')
                    @elseif(trim($__env->yieldContent('title')))
                        @yield('title')
                    @else
                        İdarəetmə Paneli
                    @endif
                </h1>
                <span style="color: #cbd5e1;">|</span>
                <span style="font-size: 0.85rem; color: #64748b;"><i class="fa-regular fa-clock"></i> {{ now()->translatedFormat('l, d F Y') }}</span>
            </div>

            <div style="display: flex; align-items: center; gap: 1rem;">
                <a href="{{ route('portal.notifications.index') }}" class="relative p-2 text-slate-500 hover:text-blue-600 transition rounded-xl hover:bg-slate-100" title="Bildirişlər">
                    <i class="fa-solid fa-bell text-lg"></i>
                    @if(Auth::user()->unreadNotificationsCount() > 0)
                        <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full ring-2 ring-white animate-pulse"></span>
                    @endif
                </a>
                <a href="{{ route('home') }}" class="inline-flex items-center px-3.5 py-1.5 border border-slate-300 rounded-xl text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 transition shadow-sm">
                    <i class="fa-solid fa-arrow-up-right-from-square mr-1.5 text-slate-400"></i> Sayta Keç
                </a>
            </div>
        </header>

        <!-- Flash Messages -->
        @if(session('success'))
            <div style="margin: 1.5rem 2rem 0; background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 0.85rem 1.25rem; border-radius: 0.75rem; display: flex; align-items: center; gap: 0.65rem;">
                <i class="fa-solid fa-circle-check text-emerald-600"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="margin: 1.5rem 2rem 0; background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 0.85rem 1.25rem; border-radius: 0.75rem; display: flex; align-items: center; gap: 0.65rem;">
                <i class="fa-solid fa-triangle-exclamation text-red-600"></i> {{ session('error') }}
            </div>
        @endif

        <!-- Portal Body -->
        <main style="padding: 2rem;">
            @yield('portal_content')
            @yield('content')
        </main>
    </div>

    @yield('scripts')
</body>
</html>
