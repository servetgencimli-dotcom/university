@extends('layouts.portal')

@section('title', 'Tələbə Müraciətləri və Apellyasiyaların İdarəsi — Xəzər Universiteti')
@section('header_title', 'Tələbə Müraciətləri & Apellyasiya İdarəsi')

@section('content')

    <!-- Top Banner -->
    <div class="glass-panel" style="padding: 2rem; margin-bottom: 2rem; background: linear-gradient(135deg, rgba(6, 17, 36, 0.9), rgba(12, 24, 44, 0.95));">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
            <div>
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                    <span class="badge badge-admin"><i class="fa-solid fa-building-columns"></i> Dekanlıq / Müəllim Nəzarəti</span>
                </div>
                <h2 class="font-heading" style="font-size: 1.8rem; color: #fff; margin-bottom: 0.25rem;">Tələbə Ərizələri və Apellyasiya Reyestri</h2>
                <p style="color: #94a3b8; font-size: 0.9rem;">
                    Tələbələr tərəfindən göndərilən imtahan nəticələrinə etirazlar, tamamlama/cərimə imtahanı və dekanlıq müraciətlərini araşdırın və rəsmi rəy bildirin.
                </p>
            </div>
        </div>
    </div>

    <!-- Appeals Table -->
    <div class="glass-panel" style="padding: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 class="font-heading" style="font-size: 1.3rem; color: #fff;">
                <i class="fa-solid fa-list-check text-sky-400"></i> Bütün Müraciətlər
            </h3>
        </div>

        @if($appeals->isEmpty())
            <div style="text-align: center; padding: 3rem 1rem; color: #64748b;">
                <i class="fa-solid fa-inbox text-4xl mb-3 opacity-40 block"></i>
                Hələ heç bir müraciət daxil olmayıb.
            </div>
        @else
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                @foreach($appeals as $app)
                    <div class="glass-card" style="padding: 1.5rem; border-left: 4px solid {{ $app->status === 'approved' ? '#34d399' : ($app->status === 'rejected' ? '#f87171' : '#fbbf24') }};">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 0.85rem;">
                            <div>
                                <span class="badge {{ $app->status_badge }}" style="margin-bottom: 0.4rem;">
                                    {{ $app->status_label }}
                                </span>
                                <h4 class="font-heading" style="font-size: 1.15rem; color: #fff;">{{ $app->subject }}</h4>
                                <div style="font-size: 0.85rem; color: #38bdf8;">
                                    <i class="fa-solid fa-user-graduate"></i> {{ $app->student->name }} ({{ $app->student->student_id }})
                                    @if($app->courseSection)
                                        • <span style="color: #cbd5e1;">{{ $app->courseSection->course->name }}</span>
                                    @endif
                                </div>
                            </div>

                            <div style="font-size: 0.8rem; color: #94a3b8; text-align: right;">
                                <i class="fa-regular fa-clock"></i> {{ $app->submitted_at?->format('d.m.Y H:i') }}
                            </div>
                        </div>

                        <!-- Appeal Reason -->
                        <div style="background: rgba(0,0,0,0.25); border-radius: 0.5rem; padding: 1rem; margin-bottom: 1rem; font-size: 0.88rem; color: #e2e8f0; line-height: 1.5;">
                            <strong>Əsaslandırma:</strong> {{ $app->reason }}
                        </div>

                        <!-- Review Form -->
                        <form action="{{ route('portal.admin.appeals.respond', $app->id) }}" method="POST" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 0.5rem; padding: 1rem;">
                            @csrf
                            <div style="display: grid; grid-template-columns: 1.2fr 1fr 2fr auto; gap: 1rem; align-items: flex-end;">
                                <div>
                                    <label style="font-size: 0.75rem; color: #94a3b8; display: block; margin-bottom: 0.25rem;">İş Axını Statusu</label>
                                    <select name="status" style="width: 100%; background: #0c1829; border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 0.5rem; border-radius: 0.4rem; font-size: 0.85rem;">
                                        <option value="in_review" {{ $app->status === 'in_review' ? 'selected' : '' }}>Fənn Müəllimindədir</option>
                                        <option value="department_review" {{ $app->status === 'department_review' ? 'selected' : '' }}>Kafedra Sədrinə Yönləndir</option>
                                        <option value="commission_review" {{ $app->status === 'commission_review' ? 'selected' : '' }}>Apellyasiya Komissiyasına Yönləndir</option>
                                        <option value="approved" {{ $app->status === 'approved' ? 'selected' : '' }}>Təsdiq Et (Qəbul)</option>
                                        <option value="rejected" {{ $app->status === 'rejected' ? 'selected' : '' }}>İmtina Et</option>
                                    </select>
                                </div>
                                <div>
                                    <label style="font-size: 0.75rem; color: #94a3b8; display: block; margin-bottom: 0.25rem;">Bal Düzəlişi (Əgər varsa)</label>
                                    <input type="number" step="0.5" name="score_adjustment" placeholder="+2.0 və ya -1.0"
                                           style="width: 100%; background: #0c1829; border: 1px solid rgba(255,255,255,0.15); color: #34d399; padding: 0.5rem; border-radius: 0.4rem; font-size: 0.85rem; font-weight: 700;">
                                </div>
                                <div>
                                    <label style="font-size: 0.75rem; color: #94a3b8; display: block; margin-bottom: 0.25rem;">Rəsmi Qeyd / Tələbəyə Cavab</label>
                                    <input type="text" name="reviewer_notes" value="{{ $app->reviewer_notes }}" placeholder="Cavab və ya komissiya qərarını qeyd edin..." required
                                           style="width: 100%; background: #0c1829; border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 0.5rem; border-radius: 0.4rem; font-size: 0.85rem;">
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1.25rem; font-size: 0.85rem;">
                                        Cavabla
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 1.5rem;">
                {{ $appeals->links() }}
            </div>
        @endif
    </div>

@endsection
