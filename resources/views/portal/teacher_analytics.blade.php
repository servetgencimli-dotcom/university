@extends('layouts.portal')

@section('title', 'Sinif Analitikası — ' . $section->course->name)
@section('header_title', 'Sinif Analitika Dashboard')

@section('content')

    {{-- Header Banner --}}
    <div class="glass-panel" style="padding: 2rem; margin-bottom: 2rem; background: linear-gradient(135deg, rgba(10,15,30,0.95), rgba(0,43,73,0.85)); border-top: 4px solid #8b5cf6;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1.5rem;">
            <div>
                <div style="display: flex; align-items: center; gap: 0.7rem; margin-bottom: 0.6rem;">
                    <span class="badge badge-primary font-mono">{{ $section->course->code }}</span>
                    <span class="badge badge-teacher"><i class="fa-solid fa-chalkboard-user"></i> {{ $section->teacher->name ?? auth()->user()->name }}</span>
                    <span class="badge badge-outline">{{ $section->semester_term }}</span>
                </div>
                <h2 class="font-heading" style="font-size: 1.75rem; color: #fff; margin-bottom: 0.25rem;">
                    <i class="fa-solid fa-chart-pie" style="color: #a78bfa;"></i>
                    {{ $section->course->name }} — Sinif Analitikası
                </h2>
                <p style="color: #94a3b8; font-size: 0.88rem;">
                    {{ $section->course->program->name ?? 'Proqram' }} • {{ $section->course->credits }} ECTS •
                    {{ $section->day_of_week }}, {{ $section->start_time }}–{{ $section->end_time }} ({{ $section->room_number }})
                </p>
            </div>
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <a href="{{ route('portal.grading.show', $section->id) }}" class="btn btn-primary" style="font-size: 0.85rem;">
                    <i class="fa-solid fa-clipboard-check"></i> Elektron Jurnal
                </a>
                <a href="{{ route('portal.grading.risk', $section->id) }}" class="btn btn-outline" style="font-size: 0.85rem; border-color: rgba(239,68,68,0.4); color: #f87171;">
                    <i class="fa-solid fa-triangle-exclamation"></i> Risk Siyahısı
                </a>
                <a href="{{ route('portal.teacher.schedule') }}" class="btn btn-outline" style="font-size: 0.85rem;">
                    <i class="fa-solid fa-arrow-left"></i> Cədvələ Qayıt
                </a>
            </div>
        </div>
    </div>

    {{-- Stat Cards Row --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
        @php
            $enrollments = $section->enrollments;
            $totalStudents = $enrollments->count();
            $graded = $enrollments->filter(fn($e) => $e->total_score > 0)->count();
            $passed = $enrollments->filter(fn($e) => $e->total_score >= 51)->count();
            $failed = $totalStudents > 0 ? ($graded - $passed) : 0;
            $avgScore = $totalStudents > 0 ? round($enrollments->avg('total_score'), 2) : 0;
            $maxScore = $enrollments->max('total_score') ?? 0;
            $minScore = $graded > 0 ? $enrollments->where('total_score', '>', 0)->min('total_score') : 0;
            $passRate = $graded > 0 ? round($passed / $graded * 100, 1) : 0;
            $atRisk = $enrollments->filter(fn($e) => $e->attendance_percentage < 75 || ($e->total_score > 0 && $e->total_score < 51))->count();

            $gradeDist = [
                'A'  => $enrollments->where('letter_grade', 'A')->count(),
                'A-' => $enrollments->where('letter_grade', 'A-')->count(),
                'B+' => $enrollments->where('letter_grade', 'B+')->count(),
                'B'  => $enrollments->where('letter_grade', 'B')->count(),
                'B-' => $enrollments->where('letter_grade', 'B-')->count(),
                'C+' => $enrollments->where('letter_grade', 'C+')->count(),
                'C'  => $enrollments->where('letter_grade', 'C')->count(),
                'C-' => $enrollments->where('letter_grade', 'C-')->count(),
                'D'  => $enrollments->where('letter_grade', 'D')->count(),
                'F'  => $enrollments->where('letter_grade', 'F')->count(),
            ];
        @endphp

        {{-- Total students --}}
        <div class="glass-card" style="padding: 1.4rem; text-align: center;">
            <div style="font-size: 2rem; color: #38bdf8; margin-bottom: 0.3rem;"><i class="fa-solid fa-users"></i></div>
            <div class="font-heading" style="font-size: 2.2rem; color: #fff; line-height: 1;">{{ $totalStudents }}</div>
            <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.3rem;">Qeydiyyatlı Tələbə</div>
        </div>

        {{-- Average score --}}
        <div class="glass-card" style="padding: 1.4rem; text-align: center;">
            <div style="font-size: 2rem; color: #fbbf24; margin-bottom: 0.3rem;"><i class="fa-solid fa-chart-line"></i></div>
            <div class="font-heading" style="font-size: 2.2rem; color: #fff; line-height: 1;">{{ $avgScore }}</div>
            <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.3rem;">Orta Bal (100-dən)</div>
        </div>

        {{-- Pass rate --}}
        <div class="glass-card" style="padding: 1.4rem; text-align: center;">
            <div style="font-size: 2rem; color: #34d399; margin-bottom: 0.3rem;"><i class="fa-solid fa-circle-check"></i></div>
            <div class="font-heading" style="font-size: 2.2rem; color: #34d399; line-height: 1;">{{ $passRate }}%</div>
            <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.3rem;">Keçmə Faizi (≥51 bal)</div>
        </div>

        {{-- Passed / failed --}}
        <div class="glass-card" style="padding: 1.4rem; text-align: center;">
            <div style="font-size: 2rem; color: #f87171; margin-bottom: 0.3rem;"><i class="fa-solid fa-circle-xmark"></i></div>
            <div style="display: flex; justify-content: center; gap: 1rem; align-items: baseline;">
                <div class="font-heading" style="font-size: 2rem; color: #34d399;">{{ $passed }}</div>
                <div style="color: #64748b;">/ </div>
                <div class="font-heading" style="font-size: 2rem; color: #f87171;">{{ $failed }}</div>
            </div>
            <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.3rem;">Keçdi / Keçmədi</div>
        </div>

        {{-- Max / Min --}}
        <div class="glass-card" style="padding: 1.4rem; text-align: center;">
            <div style="font-size: 2rem; color: #a78bfa; margin-bottom: 0.3rem;"><i class="fa-solid fa-arrow-up-wide-short"></i></div>
            <div style="display: flex; justify-content: center; gap: 0.75rem; align-items: baseline;">
                <div class="font-heading" style="font-size: 1.8rem; color: #34d399;">{{ $maxScore }}</div>
                <div style="color: #64748b;">/</div>
                <div class="font-heading" style="font-size: 1.8rem; color: #f87171;">{{ $minScore }}</div>
            </div>
            <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.3rem;">Ən Yüksək / Ən Aşağı</div>
        </div>

        {{-- At risk --}}
        <div class="glass-card" style="padding: 1.4rem; text-align: center; border: 1px solid {{ $atRisk > 0 ? 'rgba(239,68,68,0.3)' : 'rgba(255,255,255,0.06)' }};">
            <div style="font-size: 2rem; color: #f87171; margin-bottom: 0.3rem;"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <div class="font-heading" style="font-size: 2.2rem; color: {{ $atRisk > 0 ? '#f87171' : '#34d399' }}; line-height: 1;">{{ $atRisk }}</div>
            <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.3rem;">Riskli Tələbə</div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.75rem; margin-bottom: 2rem;">

        {{-- Grade Distribution Donut --}}
        <div class="glass-panel" style="padding: 2rem;">
            <h3 class="font-heading" style="font-size: 1.2rem; color: #fff; margin-bottom: 1.5rem;">
                <i class="fa-solid fa-chart-pie text-purple-400"></i> Hərf Qiyməti Paylanması
            </h3>
            <div style="position: relative; height: 260px; display: flex; align-items: center; justify-content: center;">
                <canvas id="gradeDonut" style="max-height: 260px;"></canvas>
            </div>
            {{-- Legend --}}
            <div style="display: flex; flex-wrap: wrap; gap: 0.6rem; margin-top: 1.25rem; justify-content: center;">
                @php
                    $gradeColors = ['A'=>'#34d399','A-'=>'#6ee7b7','B+'=>'#38bdf8','B'=>'#60a5fa','B-'=>'#818cf8','C+'=>'#fbbf24','C'=>'#fb923c','C-'=>'#f97316','D'=>'#f87171','F'=>'#ef4444'];
                @endphp
                @foreach($gradeDist as $grade => $cnt)
                    @if($cnt > 0)
                        <div style="display: flex; align-items: center; gap: 0.35rem; font-size: 0.78rem;">
                            <div style="width: 10px; height: 10px; border-radius: 2px; background: {{ $gradeColors[$grade] ?? '#64748b' }};"></div>
                            <span style="color: #cbd5e1;">{{ $grade }}: <strong style="color:#fff;">{{ $cnt }}</strong></span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- Pass/Fail Doughnut + Score Range Bar --}}
        <div class="glass-panel" style="padding: 2rem;">
            <h3 class="font-heading" style="font-size: 1.2rem; color: #fff; margin-bottom: 1.5rem;">
                <i class="fa-solid fa-chart-bar text-sky-400"></i> Bal Aralığı Paylanması
            </h3>
            <div style="position: relative; height: 260px;">
                <canvas id="scoreBar"></canvas>
            </div>
        </div>
    </div>

    {{-- Pass Rate Gauge & Attendance Overview --}}
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.75rem; margin-bottom: 2rem;">

        {{-- Pass rate gauge --}}
        <div class="glass-panel" style="padding: 2rem; text-align: center;">
            <h3 class="font-heading" style="font-size: 1.15rem; color: #fff; margin-bottom: 1.5rem;">
                <i class="fa-solid fa-bullseye text-emerald-400"></i> Keçmə Faizi
            </h3>
            <div style="position: relative; height: 180px; display: flex; align-items: center; justify-content: center;">
                <canvas id="passGauge" style="max-height: 180px;"></canvas>
                <div style="position: absolute; text-align: center;">
                    <div class="font-heading" style="font-size: 2rem; color: #34d399;">{{ $passRate }}%</div>
                    <div style="font-size: 0.75rem; color: #64748b;">Keçmə</div>
                </div>
            </div>
            <div style="display: flex; justify-content: center; gap: 1.5rem; margin-top: 1rem; font-size: 0.85rem;">
                <div style="color: #34d399;"><i class="fa-solid fa-circle-check"></i> Keçdi: <strong>{{ $passed }}</strong></div>
                <div style="color: #f87171;"><i class="fa-solid fa-circle-xmark"></i> Keçmədi: <strong>{{ $failed }}</strong></div>
            </div>
        </div>

        {{-- Top & Bottom Students --}}
        <div class="glass-panel" style="padding: 2rem;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                {{-- Top 5 --}}
                <div>
                    <h4 class="font-heading" style="font-size: 1rem; color: #34d399; margin-bottom: 1rem;">
                        <i class="fa-solid fa-trophy"></i> Ən Yüksək Nəticə (Top 5)
                    </h4>
                    @foreach($enrollments->sortByDesc('total_score')->take(5) as $i => $enr)
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.04);">
                            <div style="display: flex; align-items: center; gap: 0.6rem;">
                                <span style="width: 22px; height: 22px; background: {{ $i === 0 ? 'rgba(251,191,36,0.2)' : 'rgba(255,255,255,0.06)' }}; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 700; color: {{ $i === 0 ? '#fbbf24' : '#64748b' }};">{{ $i + 1 }}</span>
                                <span style="font-size: 0.82rem; color: #cbd5e1; font-weight: 500;">{{ Str::limit($enr->student->name ?? '—', 18) }}</span>
                            </div>
                            <span class="badge badge-teacher" style="font-size: 0.7rem;">{{ $enr->total_score }} / {{ $enr->letter_grade }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- Bottom 5 --}}
                <div>
                    <h4 class="font-heading" style="font-size: 1rem; color: #f87171; margin-bottom: 1rem;">
                        <i class="fa-solid fa-arrow-trend-down"></i> Ən Aşağı Nəticə (Son 5)
                    </h4>
                    @foreach($enrollments->sortBy('total_score')->take(5) as $i => $enr)
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.04);">
                            <div style="display: flex; align-items: center; gap: 0.6rem;">
                                <span style="width: 22px; height: 22px; background: rgba(239,68,68,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 700; color: #f87171;">{{ $i + 1 }}</span>
                                <span style="font-size: 0.82rem; color: #cbd5e1; font-weight: 500;">{{ Str::limit($enr->student->name ?? '—', 18) }}</span>
                            </div>
                            <span class="badge" style="background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.3); font-size: 0.7rem;">{{ $enr->total_score }} / {{ $enr->letter_grade ?? '—' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Full Student Table with Scores --}}
    <div class="glass-panel" style="padding: 2rem; margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 class="font-heading" style="font-size: 1.2rem; color: #fff;">
                <i class="fa-solid fa-table text-sky-400"></i> Tam Sinif Nəticələri ({{ $totalStudents }} Tələbə)
            </h3>
            <a href="{{ route('portal.grading.export', $section->id) }}" class="btn btn-outline" style="font-size: 0.82rem; border-color: rgba(52,211,153,0.35); color: #34d399;">
                <i class="fa-solid fa-file-excel"></i> Excel İxrac
            </a>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.83rem;">
                <thead>
                    <tr style="color: #64748b; border-bottom: 1px solid rgba(255,255,255,0.08); text-align: left;">
                        <th style="padding: 0.65rem 0.75rem;">#</th>
                        <th style="padding: 0.65rem 0.75rem;">TƏLƏBƏ</th>
                        <th style="padding: 0.65rem 0.75rem;">ID</th>
                        <th style="padding: 0.65rem 0.75rem;">ARALIQʼ</th>
                        <th style="padding: 0.65rem 0.75rem;">FINALʼ</th>
                        <th style="padding: 0.65rem 0.75rem;">YEKUNʼ</th>
                        <th style="padding: 0.65rem 0.75rem;">HƏRFİ</th>
                        <th style="padding: 0.65rem 0.75rem;">DAVAMİYYƏT</th>
                        <th style="padding: 0.65rem 0.75rem;">DURUM</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($enrollments->sortByDesc('total_score')->values() as $idx => $enr)
                        @php
                            $isRisk = $enr->attendance_percentage < 75 || ($enr->total_score > 0 && $enr->total_score < 51);
                            $lgColor = match($enr->letter_grade) {
                                'A', 'A-' => '#34d399',
                                'B+', 'B', 'B-' => '#38bdf8',
                                'C+', 'C', 'C-' => '#fbbf24',
                                'D' => '#fb923c',
                                'F' => '#f87171',
                                default => '#94a3b8',
                            };
                        @endphp
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.03); {{ $isRisk ? 'background: rgba(239,68,68,0.04);' : '' }}">
                            <td style="padding: 0.6rem 0.75rem; color: #64748b;">{{ $idx + 1 }}</td>
                            <td style="padding: 0.6rem 0.75rem; color: #fff; font-weight: 600;">
                                {{ $enr->student->name ?? '—' }}
                                @if($isRisk)
                                    <span style="font-size: 0.65rem; background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.3); padding: 0.1rem 0.35rem; border-radius: 999px; margin-left: 0.3rem;">RİSK</span>
                                @endif
                            </td>
                            <td class="font-mono" style="padding: 0.6rem 0.75rem; color: #38bdf8; font-size: 0.78rem;">{{ $enr->student->student_id ?? '—' }}</td>
                            <td style="padding: 0.6rem 0.75rem; color: #94a3b8;">{{ $enr->midterm_score }}</td>
                            <td style="padding: 0.6rem 0.75rem; color: #94a3b8;">{{ $enr->final_score }}</td>
                            <td style="padding: 0.6rem 0.75rem; font-weight: 700; color: #fff; font-size: 1rem;">{{ $enr->total_score }}</td>
                            <td style="padding: 0.6rem 0.75rem;">
                                <span style="font-weight: 800; color: {{ $lgColor }}; font-size: 0.95rem;">{{ $enr->letter_grade ?? '—' }}</span>
                            </td>
                            <td style="padding: 0.6rem 0.75rem;">
                                @php $att = $enr->attendance_percentage; @endphp
                                <div style="display: flex; align-items: center; gap: 0.6rem;">
                                    <div style="width: 60px; height: 5px; background: rgba(255,255,255,0.08); border-radius: 999px; overflow:hidden;">
                                        <div style="height:100%; width:{{ $att }}%; background: {{ $att < 75 ? '#f87171' : ($att < 85 ? '#fbbf24' : '#34d399') }}; border-radius:999px;"></div>
                                    </div>
                                    <span style="color: {{ $att < 75 ? '#f87171' : '#94a3b8' }}; font-size: 0.8rem;">{{ $att }}%</span>
                                </div>
                            </td>
                            <td style="padding: 0.6rem 0.75rem;">
                                <span class="badge" style="background: {{ $enr->total_score >= 51 ? 'rgba(16,185,129,0.15)' : 'rgba(239,68,68,0.15)' }}; color: {{ $enr->total_score >= 51 ? '#34d399' : '#f87171' }}; border: 1px solid {{ $enr->total_score >= 51 ? 'rgba(16,185,129,0.3)' : 'rgba(239,68,68,0.3)' }}; font-size: 0.7rem;">
                                    {{ $enr->total_score >= 51 ? 'Keçdi' : 'Keçmədi' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const gradeData = @json($gradeDist);
const gradeColors = {
    'A':'#34d399','A-':'#6ee7b7','B+':'#38bdf8','B':'#60a5fa',
    'B-':'#818cf8','C+':'#fbbf24','C':'#fb923c','C-':'#f97316','D':'#fbbf24','F':'#f87171'
};

// Donut chart
const donutCtx = document.getElementById('gradeDonut').getContext('2d');
new Chart(donutCtx, {
    type: 'doughnut',
    data: {
        labels: Object.keys(gradeData),
        datasets: [{
            data: Object.values(gradeData),
            backgroundColor: Object.keys(gradeData).map(k => gradeColors[k] ?? '#64748b'),
            borderColor: 'rgba(6,17,36,0.8)',
            borderWidth: 2,
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        cutout: '68%',
        responsive: true,
        maintainAspectRatio: false,
    }
});

// Score range bar chart
const scores = @json($enrollments->pluck('total_score')->values());
const ranges = { '0-25':0, '26-50':0, '51-65':0, '66-75':0, '76-85':0, '86-100':0 };
scores.forEach(s => {
    if (s <= 25)       ranges['0-25']++;
    else if (s <= 50)  ranges['26-50']++;
    else if (s <= 65)  ranges['51-65']++;
    else if (s <= 75)  ranges['66-75']++;
    else if (s <= 85)  ranges['76-85']++;
    else               ranges['86-100']++;
});
const barCtx = document.getElementById('scoreBar').getContext('2d');
new Chart(barCtx, {
    type: 'bar',
    data: {
        labels: Object.keys(ranges),
        datasets: [{
            label: 'Tələbə sayı',
            data: Object.values(ranges),
            backgroundColor: ['#f87171','#fb923c','#fbbf24','#34d399','#38bdf8','#818cf8'],
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { color: '#64748b', stepSize: 1 },
                grid: { color: 'rgba(255,255,255,0.05)' }
            },
            x: {
                ticks: { color: '#94a3b8' },
                grid: { display: false }
            }
        }
    }
});

// Pass/Fail Gauge (doughnut)
const gaugeCtx = document.getElementById('passGauge').getContext('2d');
new Chart(gaugeCtx, {
    type: 'doughnut',
    data: {
        labels: ['Keçdi', 'Keçmədi'],
        datasets: [{
            data: [{{ $passed }}, {{ max(0,$failed) }}],
            backgroundColor: ['#34d399', '#f87171'],
            borderColor: 'rgba(6,17,36,0.8)',
            borderWidth: 2,
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        cutout: '72%',
        responsive: true,
        maintainAspectRatio: false,
    }
});
</script>
@endsection
