@extends('layouts.portal')

@section('title', 'Sistem Administratoru Paneli')
@section('header_title', 'Baş Sistem Administratoru (Super Admin)')

@section('content')

    <!-- KPI Metric Cards Grid -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2.5rem;">
        
        <div class="glass-card" style="padding: 1.5rem; border-left: 4px solid #ef4444;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span style="font-size: 0.8rem; color: #94a3b8; font-weight: 600;">CƏMİ İSTİFADƏÇİLƏR</span>
                <i class="fa-solid fa-users" style="color: #f87171;"></i>
            </div>
            <div class="font-heading" style="font-size: 2rem; color: #fff;">{{ $stats['total_users'] }}</div>
            <div style="font-size: 0.75rem; color: #34d399; margin-top: 0.3rem;"><i class="fa-solid fa-arrow-trend-up"></i> Bütün rollar aktivdir</div>
        </div>

        <div class="glass-card" style="padding: 1.5rem; border-left: 4px solid #6366f1;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span style="font-size: 0.8rem; color: #94a3b8; font-weight: 600;">TƏLƏBƏ & MAGİSTR</span>
                <i class="fa-solid fa-graduation-cap" style="color: #818cf8;"></i>
            </div>
            <div class="font-heading" style="font-size: 2rem; color: #fff;">{{ $stats['students_count'] }}</div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.3rem;">Bakalavr və Magistr kontingenti</div>
        </div>

        <div class="glass-card" style="padding: 1.5rem; border-left: 4px solid #f59e0b;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span style="font-size: 0.8rem; color: #94a3b8; font-weight: 600;">QƏBUL MÜRACİƏTLƏRİ</span>
                <i class="fa-solid fa-inbox" style="color: #fbbf24;"></i>
            </div>
            <div class="font-heading" style="font-size: 2rem; color: #fff;">{{ $stats['applications_count'] }}</div>
            <div style="font-size: 0.75rem; color: #fbbf24; margin-top: 0.3rem;">{{ $stats['pending_applications'] }} baxılmamış ərizə</div>
        </div>

        <div class="glass-card" style="padding: 1.5rem; border-left: 4px solid #10b981;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span style="font-size: 0.8rem; color: #94a3b8; font-weight: 600;">ÖDƏNİLMİŞ TƏHSİL HAQQI</span>
                <i class="fa-solid fa-money-bill-wave" style="color: #34d399;"></i>
            </div>
            <div class="font-heading" style="font-size: 2rem; color: #fff;">{{ number_format($stats['total_revenue']) }} ₼</div>
            <div style="font-size: 0.75rem; color: #34d399; margin-top: 0.3rem;"><i class="fa-solid fa-check"></i> Fakturalar balanslaşdırılıb</div>
        </div>

    </div>

    <!-- Super Admin Action Center -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2.5rem;">
        <div class="glass-card" style="padding: 1.75rem; background: linear-gradient(135deg, rgba(30, 27, 75, 0.8), rgba(15, 23, 42, 0.9)); border: 1px solid rgba(129, 140, 248, 0.3);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                <div>
                    <h3 class="font-heading" style="font-size: 1.3rem; color: #fff; margin-bottom: 0.35rem;">
                        <i class="fa-solid fa-users-gear text-indigo-400"></i> İstifadəçilər, Şifrələr və İcazələr
                    </h3>
                    <p style="font-size: 0.85rem; color: #cbd5e1; line-height: 1.5;">
                        İstənilən istifadəçinin şifrəsini dəyişin, rolunu və fərdi səhifə icazələrini tənzimləyin və ya hesabına birbaşa giriş edin (impersonate).
                    </p>
                </div>
            </div>
            <a href="{{ route('portal.admin.users.index') }}" class="btn gradient-btn" style="padding: 0.6rem 1.25rem; font-size: 0.85rem;">
                <i class="fa-solid fa-sliders"></i> İstifadəçilər & Səlahiyyət Mərkəzi
            </a>
        </div>

        <div class="glass-card" style="padding: 1.75rem; background: linear-gradient(135deg, rgba(20, 83, 45, 0.4), rgba(15, 23, 42, 0.9)); border: 1px solid rgba(52, 211, 153, 0.3);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                <div>
                    <h3 class="font-heading" style="font-size: 1.3rem; color: #fff; margin-bottom: 0.35rem;">
                        <i class="fa-solid fa-file-signature text-emerald-400"></i> DİM Əsaslı Tələbə Qəbulu Reyestri
                    </h3>
                    <p style="font-size: 0.85rem; color: #cbd5e1; line-height: 1.5;">
                        Dövlət İmtahan Mərkəzindən daxil olan abituriyentləri nəzərdən keçirin, təsdiqləyin və bir kliklə rəsmi Tələbə ID-si və Kabineti yaradın.
                    </p>
                </div>
            </div>
            <a href="{{ route('portal.admin.dim.index') }}" class="btn btn-primary" style="background: #059669; border-color: #059669; padding: 0.6rem 1.25rem; font-size: 0.85rem;">
                <i class="fa-solid fa-user-graduate"></i> DİM Reyestrinə Bax & Qəbul Et
            </a>
        </div>
    </div>

    <!-- Section: Admissions Approval Pipeline -->
    <div id="applications-section" class="glass-panel" style="padding: 2rem; margin-bottom: 2.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div>
                <h3 class="font-heading" style="font-size: 1.35rem; color: #fff;"><i class="fa-solid fa-inbox text-amber-400"></i> Cari DİM və Qəbul Müraciətləri</h3>
                <div style="font-size: 0.85rem; color: #94a3b8;">Abituriyent ərizələrinin statusunu buradan dəyişdirə bilərsiniz.</div>
            </div>
            <div style="display: flex; gap: 0.75rem; align-items: center;">
                <a href="{{ route('portal.admin.dim.index') }}" class="btn btn-outline" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; color: #38bdf8;">
                    Tam Reyestrə Keç
                </a>
                <span class="badge badge-warning">{{ $recentApplications->count() }} Ərizə</span>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.1); color: #94a3b8;">
                        <th style="padding: 0.75rem 1rem;">NÖMRƏ</th>
                        <th style="padding: 0.75rem 1rem;">NAMİZƏD</th>
                        <th style="padding: 0.75rem 1rem;">İXTİSAS</th>
                        <th style="padding: 0.75rem 1rem;">BAL (İMTAHAN)</th>
                        <th style="padding: 0.75rem 1rem;">CARİ STATUS</th>
                        <th style="padding: 0.75rem 1rem;">ƏMƏLİYYAT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentApplications as $app)
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                            <td class="font-mono" style="padding: 1rem; color: #818cf8; font-weight: 700;">{{ $app->application_number }}</td>
                            <td style="padding: 1rem; font-weight: 600; color: #fff;">
                                {{ $app->full_name }}
                                <div style="font-size: 0.75rem; color: #94a3b8; font-weight: normal;">{{ $app->email }} • {{ $app->phone }}</div>
                            </td>
                            <td style="padding: 1rem; color: #cbd5e1;">
                                {{ $app->program->name }}
                                <div style="font-size: 0.75rem; color: #94a3b8;">{{ strtoupper($app->degree_level) }}</div>
                            </td>
                            <td style="padding: 1rem; font-weight: 700; color: #38bdf8;">
                                {{ $app->exam_score ?? 'N/A' }} ({{ $app->exam_type }})
                            </td>
                            <td style="padding: 1rem;">
                                @if($app->status === 'accepted')
                                    <span class="badge badge-success">Qəbul Edildi</span>
                                @elseif($app->status === 'rejected')
                                    <span class="badge badge-danger">İmtina</span>
                                @elseif($app->status === 'interview_scheduled')
                                    <span class="badge badge-warning">Müsahibə</span>
                                @else
                                    <span class="badge badge-primary">{{ $app->status }}</span>
                                @endif
                            </td>
                            <td style="padding: 1rem;">
                                <form action="{{ route('portal.applications.status', $app->id) }}" method="POST" style="display: flex; gap: 0.5rem; align-items: center;">
                                    @csrf
                                    <select name="status" class="form-select" style="font-size: 0.75rem; padding: 0.3rem 0.5rem;">
                                        <option value="under_review" {{ $app->status == 'under_review' ? 'selected' : '' }}>Yoxlanılır</option>
                                        <option value="interview_scheduled" {{ $app->status == 'interview_scheduled' ? 'selected' : '' }}>Müsahibə</option>
                                        <option value="accepted" {{ $app->status == 'accepted' ? 'selected' : '' }}>Qəbul Et</option>
                                        <option value="rejected" {{ $app->status == 'rejected' ? 'selected' : '' }}>İmtina Et</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;">Yenilə</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section: Roles and Granular Permissions Matrix -->
    <div id="users-section" class="glass-panel" style="padding: 2rem; margin-bottom: 2.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div>
                <h3 class="font-heading" style="font-size: 1.35rem; color: #fff;"><i class="fa-solid fa-shield-halved text-red-400"></i> Rollar və İcazə Matrisi (RBAC)</h3>
                <div style="font-size: 0.85rem; color: #94a3b8;">Sistemdəki 6 əsas rol və təyin olunmuş icazələr</div>
            </div>
            <span class="badge badge-system">{{ $roles->count() }} Rol</span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem;">
            @foreach($roles as $role)
                <div class="glass-card" style="padding: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                        <span class="badge 
                            @if($role->name === 'system_admin') badge-system
                            @elseif($role->name === 'admin') badge-admin
                            @elseif($role->name === 'teacher') badge-teacher
                            @elseif($role->name === 'master_student') badge-master
                            @elseif($role->name === 'student') badge-student
                            @else badge-applicant
                            @endif">
                            Səviyyə: {{ $role->level }}
                        </span>
                        <span class="font-mono" style="font-size: 0.75rem; color: #94a3b8;">{{ $role->name }}</span>
                    </div>

                    <h4 class="font-heading" style="font-size: 1.2rem; color: #fff; margin-bottom: 0.35rem;">{{ $role->label_az }}</h4>
                    <p style="font-size: 0.8rem; color: #94a3b8; margin-bottom: 1rem; line-height: 1.5;">{{ $role->description }}</p>

                    <div style="font-size: 0.75rem; color: #cbd5e1; font-weight: 600; margin-bottom: 0.5rem;">İcazələr ({{ $role->permissions->count() }}):</div>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.35rem;">
                        @foreach($role->permissions as $p)
                            <span style="font-size: 0.7rem; background: rgba(255,255,255,0.06); padding: 0.15rem 0.45rem; border-radius: 4px; color: #c7d2fe;">
                                {{ $p->label_az }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

@endsection
