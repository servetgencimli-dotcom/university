@extends('layouts.portal')

@section('title', 'Akademik Risk Siyahısı — ' . $section->course->name)
@section('header_title', 'Akademik Risk Xəbərdarlığı')

@section('content')

    {{-- Header --}}
    <div class="glass-panel" style="padding: 2rem; margin-bottom: 2rem; background: linear-gradient(135deg, rgba(20,5,5,0.95), rgba(60,10,10,0.6)); border-top: 4px solid #ef4444;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1.5rem;">
            <div>
                <div style="display: flex; align-items: center; gap: 0.7rem; margin-bottom: 0.6rem;">
                    <span class="badge badge-primary font-mono">{{ $section->course->code }}</span>
                    <span class="badge badge-teacher">{{ $section->teacher->name ?? auth()->user()->name }}</span>
                    <span class="badge badge-outline">{{ $section->semester_term }}</span>
                </div>
                <h2 class="font-heading" style="font-size: 1.75rem; color: #fff; margin-bottom: 0.3rem;">
                    <i class="fa-solid fa-triangle-exclamation" style="color: #f87171;"></i>
                    Akademik Risk Xəbərdarlığı — {{ $section->course->name }}
                </h2>
                <p style="color: #94a3b8; font-size: 0.88rem;">
                    Davamiyyəti <strong style="color:#fbbf24;">75%-dən</strong> aşağı olan
                    <strong style="color:#fbbf24;">VƏ/YA</strong>
                    yekun balı <strong style="color:#f87171;">51-dən</strong> aşağı olan tələbələrin xəbərdarlıq siyahısı.
                    Müəllim tək kliklə bütün riskli tələbələrə portal bildirişi göndərə bilər.
                </p>
            </div>
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <a href="{{ route('portal.grading.analytics', $section->id) }}" class="btn btn-primary" style="font-size: 0.85rem;">
                    <i class="fa-solid fa-chart-pie"></i> Analitika
                </a>
                <a href="{{ route('portal.grading.show', $section->id) }}" class="btn btn-outline" style="font-size: 0.85rem;">
                    <i class="fa-solid fa-clipboard-check"></i> Jurnal
                </a>
                <a href="{{ route('portal.teacher.schedule') }}" class="btn btn-outline" style="font-size: 0.85rem;">
                    <i class="fa-solid fa-arrow-left"></i> Cədvələ Qayıt
                </a>
            </div>
        </div>
    </div>

    {{-- Risk Summary Cards --}}
    @php
        $enrollments = $section->enrollments->load('student');
        $attRisk   = $enrollments->filter(fn($e) => $e->attendance_percentage < 75);
        $gradeRisk = $enrollments->filter(fn($e) => $e->total_score > 0 && $e->total_score < 51);
        $both      = $enrollments->filter(fn($e) => $e->attendance_percentage < 75 && $e->total_score > 0 && $e->total_score < 51);
        $allRisk   = $enrollments->filter(fn($e) => $e->attendance_percentage < 75 || ($e->total_score > 0 && $e->total_score < 51));
    @endphp

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
        <div class="glass-card" style="padding: 1.4rem; text-align: center; border: 1px solid rgba(239,68,68,0.25);">
            <div style="font-size: 1.8rem; color: #f87171; margin-bottom: 0.3rem;"><i class="fa-solid fa-user-xmark"></i></div>
            <div class="font-heading" style="font-size: 2.2rem; color: #f87171;">{{ $allRisk->count() }}</div>
            <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.3rem;">Toplam Riskli Tələbə</div>
        </div>
        <div class="glass-card" style="padding: 1.4rem; text-align: center; border: 1px solid rgba(251,191,36,0.2);">
            <div style="font-size: 1.8rem; color: #fbbf24; margin-bottom: 0.3rem;"><i class="fa-solid fa-user-clock"></i></div>
            <div class="font-heading" style="font-size: 2.2rem; color: #fbbf24;">{{ $attRisk->count() }}</div>
            <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.3rem;">Davamiyyət Riski (&lt;75%)</div>
        </div>
        <div class="glass-card" style="padding: 1.4rem; text-align: center; border: 1px solid rgba(239,68,68,0.2);">
            <div style="font-size: 1.8rem; color: #f87171; margin-bottom: 0.3rem;"><i class="fa-solid fa-arrow-trend-down"></i></div>
            <div class="font-heading" style="font-size: 2.2rem; color: #f87171;">{{ $gradeRisk->count() }}</div>
            <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.3rem;">Bal Riski (&lt;51 bal)</div>
        </div>
        <div class="glass-card" style="padding: 1.4rem; text-align: center; border: 1px solid rgba(239,68,68,0.35);">
            <div style="font-size: 1.8rem; color: #ef4444; margin-bottom: 0.3rem;"><i class="fa-solid fa-bomb"></i></div>
            <div class="font-heading" style="font-size: 2.2rem; color: #ef4444;">{{ $both->count() }}</div>
            <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.3rem;">Hər İki Risk</div>
        </div>
        <div class="glass-card" style="padding: 1.4rem; text-align: center;">
            <div style="font-size: 1.8rem; color: #34d399; margin-bottom: 0.3rem;"><i class="fa-solid fa-users"></i></div>
            <div class="font-heading" style="font-size: 2.2rem; color: #34d399;">{{ $enrollments->count() - $allRisk->count() }}</div>
            <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.3rem;">Risksiz Tələbə</div>
        </div>
    </div>

    {{-- Bulk Notify Action --}}
    @if($allRisk->isNotEmpty())
        <div style="background: linear-gradient(135deg, rgba(239,68,68,0.08), rgba(239,68,68,0.04)); border: 1px solid rgba(239,68,68,0.25); border-radius: 0.85rem; padding: 1.25rem 1.5rem; margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <div>
                <div style="font-weight: 700; color: #f87171; margin-bottom: 0.25rem;">
                    <i class="fa-solid fa-bell"></i> Toplu Xəbərdarlıq Bildirişi
                </div>
                <div style="font-size: 0.85rem; color: #94a3b8;">
                    Aşağıdakı <strong style="color: #fff;">{{ $allRisk->count() }}</strong> riskli tələbənin hər birinə portal vasitəsilə avtomatik xəbərdarlıq bildirişi göndərin.
                </div>
            </div>
            <form action="{{ route('portal.grading.risk.notify', $section->id) }}" method="POST" onsubmit="return confirm('{{ $allRisk->count() }} riskli tələbəyə xəbərdarlıq bildirişi göndərilsin?')">
                @csrf
                <button type="submit" class="btn" style="background: linear-gradient(135deg, #dc2626, #b91c1c); color: #fff; font-size: 0.88rem; box-shadow: 0 4px 15px rgba(239,68,68,0.3);">
                    <i class="fa-solid fa-bell"></i> Bütün Riskli Tələbələrə Bildiriş Göndər
                </button>
            </form>
        </div>
    @endif

    {{-- Risk Student Table --}}
    <div class="glass-panel" style="padding: 2rem;">
        <h3 class="font-heading" style="font-size: 1.2rem; color: #fff; margin-bottom: 1.5rem;">
            <i class="fa-solid fa-list-ul text-red-400"></i>
            Riskli Tələbə Siyahısı ({{ $allRisk->count() }} Tələbə)
        </h3>

        @if($allRisk->isEmpty())
            <div style="text-align: center; padding: 3rem; color: #34d399;">
                <i class="fa-solid fa-circle-check" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                <h3 class="font-heading" style="font-size: 1.5rem; margin-bottom: 0.5rem;">Əla! Heç bir riskli tələbə yoxdur.</h3>
                <p style="color: #64748b;">Bu fənndə bütün tələbələrin davamiyyəti ≥75% və balları yetərlidir.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.84rem;">
                    <thead>
                        <tr style="color: #64748b; border-bottom: 1px solid rgba(255,255,255,0.08); text-align: left;">
                            <th style="padding: 0.65rem 0.75rem;">#</th>
                            <th style="padding: 0.65rem 0.75rem;">TƏLƏBƏ</th>
                            <th style="padding: 0.65rem 0.75rem;">ID</th>
                            <th style="padding: 0.65rem 0.75rem;">ARALIQʼ</th>
                            <th style="padding: 0.65rem 0.75rem;">FINALʼ</th>
                            <th style="padding: 0.65rem 0.75rem;">YEKUNʼ</th>
                            <th style="padding: 0.65rem 0.75rem;">DAVAMİYYƏT</th>
                            <th style="padding: 0.65rem 0.75rem;">RİSK SEBƏBİ</th>
                            <th style="padding: 0.65rem 0.75rem;">BİLDİRİŞ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allRisk->sortBy('attendance_percentage')->values() as $idx => $enr)
                            @php
                                $isAttRisk   = $enr->attendance_percentage < 75;
                                $isGradeRisk = $enr->total_score > 0 && $enr->total_score < 51;
                                $isBoth      = $isAttRisk && $isGradeRisk;
                                $rowColor = $isBoth ? 'rgba(239,68,68,0.07)' : ($isAttRisk ? 'rgba(251,191,36,0.05)' : 'rgba(239,68,68,0.04)');
                            @endphp
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.04); background: {{ $rowColor }};">
                                <td style="padding: 0.7rem 0.75rem; color: #64748b;">{{ $idx + 1 }}</td>
                                <td style="padding: 0.7rem 0.75rem; font-weight: 700; color: #fff;">
                                    {{ $enr->student->name ?? '—' }}
                                </td>
                                <td class="font-mono" style="padding: 0.7rem 0.75rem; color: #38bdf8; font-size: 0.78rem;">{{ $enr->student->student_id ?? '—' }}</td>
                                <td style="padding: 0.7rem 0.75rem; color: #94a3b8;">{{ $enr->midterm_score }}</td>
                                <td style="padding: 0.7rem 0.75rem; color: #94a3b8;">{{ $enr->final_score }}</td>
                                <td style="padding: 0.7rem 0.75rem;">
                                    <span style="font-weight: 800; color: {{ $enr->total_score < 51 && $enr->total_score > 0 ? '#f87171' : '#fff' }}; font-size: 1rem;">
                                        {{ $enr->total_score }}
                                    </span>
                                </td>
                                <td style="padding: 0.7rem 0.75rem;">
                                    <div style="display: flex; align-items: center; gap: 0.6rem;">
                                        <div style="width: 55px; height: 5px; background: rgba(255,255,255,0.08); border-radius: 999px; overflow: hidden;">
                                            <div style="height:100%; width:{{ $enr->attendance_percentage }}%; background: {{ $isAttRisk ? '#f87171' : '#34d399' }}; border-radius:999px;"></div>
                                        </div>
                                        <span style="color: {{ $isAttRisk ? '#f87171' : '#34d399' }}; font-weight: 700;">{{ $enr->attendance_percentage }}%</span>
                                    </div>
                                </td>
                                <td style="padding: 0.7rem 0.75rem;">
                                    @if($isBoth)
                                        <span class="badge" style="background: rgba(239,68,68,0.2); color: #f87171; border: 1px solid rgba(239,68,68,0.4); font-size: 0.68rem;">
                                            <i class="fa-solid fa-bomb"></i> Hər İki Risk
                                        </span>
                                    @elseif($isAttRisk)
                                        <span class="badge" style="background: rgba(251,191,36,0.15); color: #fbbf24; border: 1px solid rgba(251,191,36,0.3); font-size: 0.68rem;">
                                            <i class="fa-solid fa-user-clock"></i> Davamiyyət
                                        </span>
                                    @else
                                        <span class="badge" style="background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.3); font-size: 0.68rem;">
                                            <i class="fa-solid fa-arrow-trend-down"></i> Bal
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 0.7rem 0.75rem;">
                                    <form action="{{ route('portal.grading.risk.notify-one', ['sectionId' => $section->id, 'enrollmentId' => $enr->id]) }}" method="POST" style="margin: 0;">
                                        @csrf
                                        <button type="submit" class="btn btn-outline" style="font-size: 0.72rem; padding: 0.28rem 0.7rem; border-color: rgba(251,191,36,0.3); color: #fbbf24;" title="Bu tələbəyə xəbərdarlıq bildirişi göndər">
                                            <i class="fa-solid fa-bell"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

@endsection
