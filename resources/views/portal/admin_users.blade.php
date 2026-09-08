@extends('layouts.portal')

@section('title', 'İstifadəçilər və İcazələr — Super Admin')
@section('header_title', 'İstifadəçilərin İdarəsi, Şifrə və Səlahiyyət Matrisi')

@section('content')

    <!-- Top Action Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 class="font-heading" style="font-size: 1.6rem; color: #fff;">Bütün İstifadəçilər ({{ $users->total() }})</h2>
            <p style="color: #94a3b8; font-size: 0.9rem;">Sistemdəki bütün müəllim, tələbə, dekan və inzibati heyətin fərdi icazələrini və şifrələrini idarə edin.</p>
        </div>

        <button onclick="document.getElementById('modal-create-user').style.display='flex'" class="btn gradient-btn" style="padding: 0.65rem 1.25rem;">
            <i class="fa-solid fa-user-plus"></i> Yeni İstifadəçi Yarat
        </button>
    </div>

    <!-- Filter & Search Panel -->
    <div class="glass-panel" style="padding: 1.5rem; margin-bottom: 2rem;">
        <form method="GET" action="{{ route('portal.admin.users.index') }}" style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 1rem; align-items: flex-end;">
            <div>
                <label style="font-size: 0.8rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Axtarış (Ad, E-poçt, Tələbə/Əməkdaş ID)</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Məsələn: Murad, KU-2023..." class="form-input">
            </div>

            <div>
                <label style="font-size: 0.8rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Rol</label>
                <select name="role" class="form-select">
                    <option value="">Bütün Rollar</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->name }}" {{ request('role') === $r->name ? 'selected' : '' }}>{{ $r->label_az }} ({{ $r->name }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 0.8rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Məktəb / Fakültə</label>
                <select name="faculty_id" class="form-select">
                    <option value="">Bütün Məktəblər</option>
                    @foreach($faculties as $f)
                        <option value="{{ $f->id }}" {{ request('faculty_id') == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.25rem; height: 42px;">
                    <i class="fa-solid fa-filter"></i> Süzgəc
                </button>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="glass-panel" style="padding: 1.5rem;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.1); color: #94a3b8; font-size: 0.8rem; text-transform: uppercase;">
                        <th style="padding: 0.75rem 1rem;">İSTİFADƏÇİ</th>
                        <th style="padding: 0.75rem 1rem;">ROL & STATUS</th>
                        <th style="padding: 0.75rem 1rem;">ID / KOD</th>
                        <th style="padding: 0.75rem 1rem;">MƏKTƏB / KAFEDRA</th>
                        <th style="padding: 0.75rem 1rem;">FƏRDİ İCAZƏLƏR</th>
                        <th style="padding: 0.75rem 1rem; text-align: right;">SUPER ADMİN ƏMƏLİYYATLARI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                            <td style="padding: 1rem;">
                                <div style="font-weight: 700; color: #fff; font-size: 0.95rem;">{{ $u->name }}</div>
                                <div style="font-size: 0.8rem; color: #38bdf8;">{{ $u->email }}</div>
                                @if($u->phone)
                                    <div style="font-size: 0.75rem; color: #94a3b8;">{{ $u->phone }}</div>
                                @endif
                            </td>

                            <td style="padding: 1rem;">
                                <div>
                                    <span class="badge 
                                        @if($u->role === 'system_admin') badge-system
                                        @elseif($u->role === 'admin') badge-admin
                                        @elseif($u->role === 'teacher') badge-teacher
                                        @elseif($u->role === 'master_student') badge-master
                                        @elseif($u->role === 'student') badge-student
                                        @else badge-applicant
                                        @endif">
                                        {{ $u->role_title ?? $u->role }}
                                    </span>
                                </div>
                                <div style="margin-top: 0.35rem;">
                                    @if($u->status === 'active')
                                        <span style="font-size: 0.75rem; color: #34d399;"><i class="fa-solid fa-circle" style="font-size: 0.5rem;"></i> Aktiv</span>
                                    @elseif($u->status === 'pending')
                                        <span style="font-size: 0.75rem; color: #fbbf24;"><i class="fa-solid fa-circle" style="font-size: 0.5rem;"></i> Gözləmədə</span>
                                    @else
                                        <span style="font-size: 0.75rem; color: #f87171;"><i class="fa-solid fa-circle" style="font-size: 0.5rem;"></i> {{ ucfirst($u->status) }}</span>
                                    @endif
                                </div>
                            </td>

                            <td style="padding: 1rem;">
                                @if($u->student_id)
                                    <div class="font-mono" style="font-size: 0.85rem; color: #a5b4fc; font-weight: 700;">{{ $u->student_id }}</div>
                                    <div style="font-size: 0.75rem; color: #94a3b8;">{{ strtoupper($u->degree_level ?? 'Bakalavr') }}</div>
                                @elseif($u->staff_id)
                                    <div class="font-mono" style="font-size: 0.85rem; color: #38bdf8; font-weight: 700;">{{ $u->staff_id }}</div>
                                    <div style="font-size: 0.75rem; color: #94a3b8;">Əməkdaş</div>
                                @else
                                    <span style="color: #64748b; font-size: 0.8rem;">—</span>
                                @endif
                            </td>

                            <td style="padding: 1rem;">
                                @if($u->faculty)
                                    <div style="font-size: 0.85rem; color: #cbd5e1; font-weight: 600;">{{ $u->faculty->name }}</div>
                                    @if($u->department)
                                        <div style="font-size: 0.75rem; color: #94a3b8;">{{ $u->department->name }}</div>
                                    @endif
                                @else
                                    <span style="color: #64748b; font-size: 0.8rem;">Məktəb təyin edilməyib</span>
                                @endif
                            </td>

                            <td style="padding: 1rem;">
                                @if(is_array($u->permissions) && count($u->permissions) > 0)
                                    <span class="badge badge-primary" style="font-size: 0.75rem;">
                                        {{ count($u->permissions) }} Fərdi İcazə
                                    </span>
                                @else
                                    <span style="font-size: 0.8rem; color: #94a3b8;">Rol standartı</span>
                                @endif
                            </td>

                            <td style="padding: 1rem; text-align: right;">
                                <div style="display: flex; gap: 0.5rem; justify-content: flex-end; align-items: center;">
                                    
                                    <!-- Impersonate Button -->
                                    @if($u->id !== Auth::id())
                                        <form action="{{ route('portal.admin.impersonate', $u->id) }}" method="POST" style="margin: 0;">
                                            @csrf
                                            <button type="submit" class="btn btn-outline" style="padding: 0.35rem 0.65rem; font-size: 0.8rem; color: #38bdf8; border-color: rgba(56, 189, 248, 0.4);" title="Bu istifadəçi kimi daxil ol">
                                                <i class="fa-solid fa-user-secret"></i> Giriş
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Change Password Button -->
                                    <button onclick="openPasswordModal({{ $u->id }}, '{{ addslashes($u->name) }}')" class="btn btn-outline" style="padding: 0.35rem 0.65rem; font-size: 0.8rem; color: #fbbf24; border-color: rgba(251, 191, 36, 0.4);" title="Şifrəni Dəyiş">
                                        <i class="fa-solid fa-key"></i> Şifrə
                                    </button>

                                    <!-- Manage Roles & Permissions Button -->
                                    <button onclick="openPermissionsModal({{ json_encode($u) }})" class="btn btn-primary" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;" title="Rol və İcazələri Tənzimlə">
                                        <i class="fa-solid fa-sliders"></i> Səlahiyyət
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem; color: #94a3b8;">
                                Axtarışa uyğun istifadəçi tapılmadı.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 1.5rem;">
            {{ $users->links() }}
        </div>
    </div>

    <!-- MODAL 1: Create User -->
    <div id="modal-create-user" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
        <div class="glass-panel" style="background: #091322; border: 1px solid rgba(56, 189, 248, 0.4); border-radius: 1rem; width: 100%; max-width: 650px; max-height: 90vh; overflow-y: auto; padding: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 1rem;">
                <h3 class="font-heading" style="font-size: 1.35rem; color: #fff;"><i class="fa-solid fa-user-plus text-sky-400"></i> Yeni İstifadəçi Qeydiyyatı</h3>
                <button onclick="document.getElementById('modal-create-user').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.2rem; cursor: pointer;">&times;</button>
            </div>

            <form action="{{ route('portal.admin.users.store') }}" method="POST">
                @csrf

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Tam Adı (Ad Soyad)</label>
                        <input type="text" name="name" required placeholder="Məsələn: Əli Məmmədov" class="form-input">
                    </div>
                    <div>
                        <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">E-poçt Ünvanı (@khazar.org)</label>
                        <input type="email" name="email" required placeholder="ali.mammadov@khazar.org" class="form-input">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">İlkin Şifrə</label>
                        <input type="password" name="password" required placeholder="Minimum 6 simvol" class="form-input">
                    </div>
                    <div>
                        <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Əsas Rol</label>
                        <select name="role" required class="form-select">
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}">{{ $r->label_az }} ({{ $r->name }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Vəzifə / Rol Başlığı</label>
                        <input type="text" name="role_title" placeholder="Məs: Kompüter Elmləri Kafedra Müdiri" class="form-input">
                    </div>
                    <div>
                        <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Tələbə ID və ya Əməkdaş ID</label>
                        <input type="text" name="student_id" placeholder="Məs: KU-2026-1042" class="form-input">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Məktəb / Fakültə</label>
                        <select name="faculty_id" class="form-select">
                            <option value="">Seçilməyib</option>
                            @foreach($faculties as $f)
                                <option value="{{ $f->id }}">{{ $f->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Hesab Statusu</label>
                        <select name="status" class="form-select">
                            <option value="active">Aktiv</option>
                            <option value="pending">Gözləmədə</option>
                            <option value="suspended">Dayandırılıb</option>
                            <option value="graduated">Məzun</option>
                        </select>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1.5rem;">
                    <button type="button" onclick="document.getElementById('modal-create-user').style.display='none'" class="btn btn-outline" style="padding: 0.6rem 1.25rem;">Ləğv et</button>
                    <button type="submit" class="btn gradient-btn" style="padding: 0.6rem 1.5rem;">İstifadəçini Yarat</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: Change Password -->
    <div id="modal-change-password" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
        <div class="glass-panel" style="background: #091322; border: 1px solid rgba(251, 191, 36, 0.4); border-radius: 1rem; width: 100%; max-width: 480px; padding: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <h3 class="font-heading" style="font-size: 1.3rem; color: #fff;"><i class="fa-solid fa-key text-amber-400"></i> Şifrəni Yenilə</h3>
                <button onclick="document.getElementById('modal-change-password').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.2rem; cursor: pointer;">&times;</button>
            </div>

            <p style="font-size: 0.85rem; color: #cbd5e1; margin-bottom: 1.25rem;">
                İstifadəçi: <strong id="pwd-user-name" style="color: #38bdf8;"></strong>
            </p>

            <form id="form-change-password" method="POST" action="">
                @csrf
                <div style="margin-bottom: 1.5rem;">
                    <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.4rem;">Yeni Şifrə Təyin Et</label>
                    <input type="password" name="password" required placeholder="Minimum 6 simvol" class="form-input">
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" onclick="document.getElementById('modal-change-password').style.display='none'" class="btn btn-outline" style="padding: 0.5rem 1rem;">Bağla</button>
                    <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1.25rem; background: #d97706; border-color: #d97706;">Şifrəni Saxla</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: Manage Roles & Granular Permissions -->
    <div id="modal-permissions" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
        <div class="glass-panel" style="background: #091322; border: 1px solid rgba(129, 140, 248, 0.4); border-radius: 1rem; width: 100%; max-width: 750px; max-height: 90vh; overflow-y: auto; padding: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 0.75rem;">
                <h3 class="font-heading" style="font-size: 1.35rem; color: #fff;"><i class="fa-solid fa-shield-halved text-indigo-400"></i> Rol və Fərdi Səhifə İcazələri</h3>
                <button onclick="document.getElementById('modal-permissions').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.2rem; cursor: pointer;">&times;</button>
            </div>

            <form id="form-permissions" method="POST" action="">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                    <div>
                        <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">İstifadəçinin Əsas Rolu</label>
                        <select id="perm-role" name="role" required class="form-select">
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}">{{ $r->label_az }} ({{ $r->name }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Status</label>
                        <select id="perm-status" name="status" class="form-select">
                            <option value="active">Aktiv</option>
                            <option value="pending">Gözləmədə</option>
                            <option value="suspended">Dayandırılıb</option>
                            <option value="graduated">Məzun</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Rol / Vəzifə Başlığı</label>
                    <input id="perm-role-title" type="text" name="role_title" class="form-input">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <div style="font-size: 0.9rem; font-weight: 700; color: #fff; margin-bottom: 0.5rem;">
                        <i class="fa-solid fa-list-check text-sky-400"></i> Fərdi Səhifə və Modul İcazələri (Override):
                    </div>
                    <p style="font-size: 0.75rem; color: #94a3b8; margin-bottom: 1rem;">System admin istifadəçiyə rolundan asılı olmayaraq əlavə səhifə çıxışları və funksiyalar verə bilər.</p>

                    @foreach($permissions as $groupName => $groupPerms)
                        <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem; padding: 1rem; margin-bottom: 0.75rem;">
                            <div style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: #38bdf8; margin-bottom: 0.65rem;">
                                {{ strtoupper($groupName) }} MODULLARI
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem;">
                                @foreach($groupPerms as $p)
                                    <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; color: #cbd5e1; cursor: pointer;">
                                        <input type="checkbox" name="permissions[]" value="{{ $p->name }}" class="perm-checkbox" style="accent-color: #00A3E0;">
                                        <span>{{ $p->label_az }} <span style="color: #64748b; font-size: 0.7rem;">({{ $p->name }})</span></span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" onclick="document.getElementById('modal-permissions').style.display='none'" class="btn btn-outline" style="padding: 0.5rem 1rem;">Bağla</button>
                    <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1.5rem;">İcazələri Təsdiqlə</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    function openPasswordModal(userId, userName) {
        document.getElementById('pwd-user-name').innerText = userName;
        document.getElementById('form-change-password').action = `/portal/admin/users/${userId}/password`;
        document.getElementById('modal-change-password').style.display = 'flex';
    }

    function openPermissionsModal(user) {
        document.getElementById('perm-role').value = user.role;
        document.getElementById('perm-status').value = user.status;
        document.getElementById('perm-role-title').value = user.role_title || '';
        document.getElementById('form-permissions').action = `/portal/admin/users/${user.id}/permissions`;

        // Clear and set checkboxes
        const checkboxes = document.querySelectorAll('.perm-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = false;
            if (user.permissions && Array.isArray(user.permissions) && user.permissions.includes(cb.value)) {
                cb.checked = true;
            }
        });

        document.getElementById('modal-permissions').style.display = 'flex';
    }
</script>
@endsection
