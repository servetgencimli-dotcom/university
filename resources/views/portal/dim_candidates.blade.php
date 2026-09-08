@extends('layouts.portal')

@section('title', 'DİM Qəbul Reyestri — Khazar University')
@section('header_title', 'DİM (Dövlət İmtahan Mərkəzi) Əsaslı Tələbə Qəbulu Reyestri')

@section('content')

    <!-- Top Action Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 class="font-heading" style="font-size: 1.6rem; color: #fff;">DİM Namizədlər Reyestri ({{ $candidates->total() }})</h2>
            <p style="color: #94a3b8; font-size: 0.9rem;">DİM mərkəzləşdirilmiş imtahan nəticələri üzrə Xəzər Universitetinə yönləndirilmiş abituriyentlərin qəbul təsdiqi.</p>
        </div>

        <button onclick="document.getElementById('modal-add-dim').style.display='flex'" class="btn gradient-btn" style="padding: 0.65rem 1.25rem;">
            <i class="fa-solid fa-plus-circle"></i> Yeni DİM Protokolu / Namizəd Əlavə Et
        </button>
    </div>

    <!-- Filter & Search Panel -->
    <div class="glass-panel" style="padding: 1.5rem; margin-bottom: 2rem;">
        <form method="GET" action="{{ route('portal.admin.dim.index') }}" style="display: grid; grid-template-columns: 2fr 1.5fr 1fr auto; gap: 1rem; align-items: flex-end;">
            <div>
                <label style="font-size: 0.8rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Axtarış (Ad, FİN, DİM İş №)</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Məsələn: Kənan, 7ABC991, 260492..." class="form-input">
            </div>

            <div>
                <label style="font-size: 0.8rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">İxtisas / Proqram</label>
                <select name="program_id" class="form-select">
                    <option value="">Bütün İxtisaslar</option>
                    @foreach($programs as $p)
                        <option value="{{ $p->id }}" {{ request('program_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 0.8rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Status</label>
                <select name="status" class="form-select">
                    <option value="">Bütün Statuslar</option>
                    <option value="under_review" {{ request('status') === 'under_review' ? 'selected' : '' }}>Gözləmədə / Yoxlanılır</option>
                    <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Qəbul Edildi (Tələbə)</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>İmtina</option>
                </select>
            </div>

            <div>
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.25rem; height: 42px;">
                    <i class="fa-solid fa-filter"></i> Süzgəc
                </button>
            </div>
        </form>
    </div>

    <!-- Candidates Table -->
    <div class="glass-panel" style="padding: 1.5rem;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.1); color: #94a3b8; font-size: 0.8rem; text-transform: uppercase;">
                        <th style="padding: 0.75rem 1rem;">DİM İŞ № / FİN</th>
                        <th style="padding: 0.75rem 1rem;">NAMİZƏD</th>
                        <th style="padding: 0.75rem 1rem;">İXTİSAS (PROQRAM)</th>
                        <th style="padding: 0.75rem 1rem;">DİM BALI & FORMA</th>
                        <th style="padding: 0.75rem 1rem;">STATUS</th>
                        <th style="padding: 0.75rem 1rem; text-align: right;">QƏBUL ƏMƏLİYYATLARI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($candidates as $c)
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                            <td style="padding: 1rem;">
                                <div class="font-mono" style="color: #fbbf24; font-weight: 700;">{{ $c->dim_work_number ?? $c->application_number }}</div>
                                <div style="font-size: 0.75rem; color: #94a3b8;">FİN: <strong style="color: #cbd5e1;">{{ $c->passport_fin ?? '—' }}</strong></div>
                            </td>

                            <td style="padding: 1rem;">
                                <div style="font-weight: 700; color: #fff; font-size: 0.95rem;">{{ $c->full_name }}</div>
                                <div style="font-size: 0.8rem; color: #38bdf8;">{{ $c->email }} • {{ $c->phone }}</div>
                            </td>

                            <td style="padding: 1rem;">
                                <div style="font-size: 0.85rem; color: #cbd5e1; font-weight: 600;">{{ $c->program->name }}</div>
                                <div style="font-size: 0.75rem; color: #94a3b8;">{{ strtoupper($c->degree_level) }} • {{ $c->program->department->faculty->name ?? '' }}</div>
                            </td>

                            <td style="padding: 1rem;">
                                <div style="font-size: 1.1rem; font-weight: 700; color: #34d399;">{{ $c->exam_score ?? '—' }} <span style="font-size: 0.75rem; color: #94a3b8;">bal</span></div>
                                <div style="font-size: 0.75rem;">
                                    @if($c->odenis_formasi === 'Dövlət Sifarişli')
                                        <span class="badge badge-success" style="font-size: 0.7rem;">Dövlət Sifarişli (Təqaüdlü)</span>
                                    @else
                                        <span class="badge badge-warning" style="font-size: 0.7rem;">Ödənişli</span>
                                    @endif
                                </div>
                            </td>

                            <td style="padding: 1rem;">
                                @if($c->status === 'accepted')
                                    <span class="badge badge-success"><i class="fa-solid fa-check-circle"></i> Tələbə Qəbul Edildi</span>
                                    @if($c->user)
                                        <div class="font-mono" style="font-size: 0.75rem; color: #a5b4fc; margin-top: 0.3rem;">ID: {{ $c->user->student_id }}</div>
                                    @endif
                                @elseif($c->status === 'rejected')
                                    <span class="badge badge-danger"><i class="fa-solid fa-times-circle"></i> İmtina</span>
                                @else
                                    <span class="badge badge-warning"><i class="fa-solid fa-hourglass-half"></i> Qərar Gözləyir</span>
                                @endif
                            </td>

                            <td style="padding: 1rem; text-align: right;">
                                <div style="display: flex; gap: 0.5rem; justify-content: flex-end; align-items: center;">
                                    @if($c->status !== 'accepted')
                                        <!-- Accept button -->
                                        <form action="{{ route('portal.admin.dim.accept', $c->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Bu namizədi təsdiq edib rəsmi tələbə hesabı yaratmaq istəyirsiniz?');">
                                            @csrf
                                            <button type="submit" class="btn btn-primary" style="padding: 0.4rem 0.85rem; font-size: 0.8rem; background: #059669; border-color: #059669;">
                                                <i class="fa-solid fa-user-graduate"></i> Qəbul Et & Tələbə Yarat
                                            </button>
                                        </form>

                                        <!-- Reject button -->
                                        <form action="{{ route('portal.admin.dim.reject', $c->id) }}" method="POST" style="margin: 0;">
                                            @csrf
                                            <button type="submit" class="btn btn-outline" style="padding: 0.4rem 0.65rem; font-size: 0.8rem; color: #f87171; border-color: rgba(239, 68, 68, 0.4);">
                                                İmtina
                                            </button>
                                        </form>
                                    @else
                                        @if($c->user)
                                            <!-- If already student, allow quick impersonate or view -->
                                            <form action="{{ route('portal.admin.impersonate', $c->user->id) }}" method="POST" style="margin: 0;">
                                                @csrf
                                                <button type="submit" class="btn btn-outline" style="padding: 0.4rem 0.75rem; font-size: 0.8rem; color: #38bdf8; border-color: rgba(56, 189, 248, 0.4);">
                                                    <i class="fa-solid fa-user-secret"></i> Kabinetə Bax
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem; color: #94a3b8;">
                                DİM namizəd reyestrində heç bir qeyd tapılmadı.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 1.5rem;">
            {{ $candidates->links() }}
        </div>
    </div>

    <!-- MODAL: Add DIM Candidate -->
    <div id="modal-add-dim" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
        <div class="glass-panel" style="background: #091322; border: 1px solid rgba(251, 191, 36, 0.4); border-radius: 1rem; width: 100%; max-width: 650px; max-height: 90vh; overflow-y: auto; padding: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 1rem;">
                <h3 class="font-heading" style="font-size: 1.35rem; color: #fff;"><i class="fa-solid fa-file-circle-plus text-amber-400"></i> DİM Namizəd Qeydiyyatı</h3>
                <button onclick="document.getElementById('modal-add-dim').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.2rem; cursor: pointer;">&times;</button>
            </div>

            <form action="{{ route('portal.admin.dim.store') }}" method="POST">
                @csrf

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">DİM İş Nömrəsi</label>
                        <input type="text" name="dim_work_number" required placeholder="Məs: 2608412" class="form-input">
                    </div>
                    <div>
                        <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Şəxsiyyət Vəsiqəsi FİN</label>
                        <input type="text" name="passport_fin" required placeholder="Məs: 7ABC991" class="form-input" style="text-transform: uppercase;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Adı</label>
                        <input type="text" name="first_name" required placeholder="Kənan" class="form-input">
                    </div>
                    <div>
                        <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Soyadı</label>
                        <input type="text" name="last_name" required placeholder="Qasımov" class="form-input">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">E-poçt Ünvanı</label>
                        <input type="email" name="email" required placeholder="kenan.qasimov@gmail.com" class="form-input">
                    </div>
                    <div>
                        <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Əlaqə Telefonu</label>
                        <input type="text" name="phone" required placeholder="+994 50 123 45 67" class="form-input">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Qəbul Olduğu İxtisas</label>
                        <select name="program_id" required class="form-select">
                            @foreach($programs as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} ({{ strtoupper($p->degree_level) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Təhsil Pilləsi</label>
                        <select name="degree_level" class="form-select">
                            <option value="bachelor">Bakalavr</option>
                            <option value="master">Magistr</option>
                            <option value="phd">Doktorantura</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">DİM İmtahan Balı</label>
                        <input type="number" step="1" min="0" max="700" name="exam_score" required placeholder="Məs: 595" class="form-input">
                    </div>
                    <div>
                        <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">İxtisas Seçim Sırası</label>
                        <input type="number" step="1" min="1" max="15" name="secim_sira" value="1" class="form-input">
                    </div>
                    <div>
                        <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Ödəniş Forması</label>
                        <select name="odenis_formasi" class="form-select">
                            <option value="Dövlət Sifarişli">Dövlət Sifarişli</option>
                            <option value="Ödənişli Əsaslarla">Ödənişli</option>
                        </select>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                    <button type="button" onclick="document.getElementById('modal-add-dim').style.display='none'" class="btn btn-outline" style="padding: 0.6rem 1.25rem;">Ləğv et</button>
                    <button type="submit" class="btn gradient-btn" style="padding: 0.6rem 1.5rem;">Reyestrə Əlavə Et</button>
                </div>
            </form>
        </div>
    </div>

@endsection
