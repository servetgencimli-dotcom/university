@extends('layouts.portal')

@section('title', 'Bal Bölgüsü və Qiymətləndirmə Jurnalı — Xəzər Universiteti')
@section('header_title', 'Fənn Bal Bölgüsü & Elektron Qiymətləndirmə')

@section('content')

    <!-- Top Section Banner -->
    <div class="glass-panel" style="padding: 2rem; margin-bottom: 2rem; background: linear-gradient(135deg, rgba(6, 17, 36, 0.9), rgba(12, 24, 44, 0.95));">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
            <div>
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                    <span class="font-mono badge badge-primary" style="font-size: 0.85rem;">{{ $section->course->code }}</span>
                    <span class="badge badge-teacher"><i class="fa-solid fa-chalkboard-user"></i> {{ $section->teacher->name ?? 'Müəllim' }}</span>
                    <span class="badge badge-outline">{{ $section->semester_term }}</span>
                </div>
                <h2 class="font-heading" style="font-size: 1.8rem; color: #fff; margin-bottom: 0.25rem;">{{ $section->course->name }}</h2>
                <div style="font-size: 0.85rem; color: #94a3b8;">
                    {{ $section->course->program->name ?? 'Proqram' }} • {{ $section->course->credits }} ECTS • {{ $section->day_of_week }}, {{ $section->start_time }} - {{ $section->end_time }} ({{ $section->room_number }})
                </div>
            </div>

            <div style="display: flex; gap: 0.75rem; align-items: center;">
                <a href="{{ route('portal.dashboard') }}" class="btn btn-outline" style="font-size: 0.85rem;">
                    <i class="fa-solid fa-arrow-left"></i> Müəllim Panelinə Qayıt
                </a>
                <a href="{{ route('portal.lms.course', $section->id) }}" class="btn btn-primary" style="font-size: 0.85rem;">
                    <i class="fa-solid fa-book-open"></i> Fənnin LMS Otağı
                </a>
            </div>
        </div>
    </div>

    <!-- PART 1: GRADE DISTRIBUTION CONFIGURATION (BAL BÖLGÜSÜ TƏYİNİ) -->
    <div class="glass-panel" style="padding: 2rem; margin-bottom: 2.5rem; border-top: 4px solid #00A3E0;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 1rem;">
            <div>
                <h3 class="font-heading" style="font-size: 1.35rem; color: #fff; display: flex; align-items: center; gap: 0.6rem;">
                    <i class="fa-solid fa-sliders text-sky-400"></i> Fənn üzrə Bal Bölgüsü Konfiqurasiyası
                </h3>
                <p style="font-size: 0.85rem; color: #94a3b8; margin-top: 0.25rem;">
                    Bu fənn üçün istədiyiniz bal komponentlərini (davamiyyət, seminar, laboratoriya, sərbəst iş, quiz, imtahan) və onların maksimal ballarını təyin edin.
                </p>
            </div>

            <div style="display: flex; align-items: center; gap: 1rem;">
                <form action="{{ route('portal.grading.components.template', $section->id) }}" method="POST" onsubmit="return confirm('Standart şablon tətbiq edilsin? (10 Davamiyyət, 10 Seminar, 10 Sərbəst İş, 20 Kollokvium, 50 Final)')">
                    @csrf
                    <button type="submit" class="btn btn-outline" style="font-size: 0.82rem; border-color: rgba(56, 189, 248, 0.4); color: #38bdf8;">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> Standart Şablonu Tətbiq Et (10-10-10-20-50)
                    </button>
                </form>
            </div>
        </div>

        <form action="{{ route('portal.grading.components.save', $section->id) }}" method="POST" id="components-form">
            @csrf

            <div id="components-container" style="display: flex; flex-direction: column; gap: 0.85rem; margin-bottom: 1.5rem;">
                @if($components->isEmpty())
                    <!-- Default initial 5 items if empty -->
                    @php
                        $defaultComponents = [
                            ['name' => 'Davamiyyət və Dərsdə Fəallıq', 'max_score' => 10, 'type' => 'attendance'],
                            ['name' => 'Seminar və Məşğələ Dərsləri', 'max_score' => 10, 'type' => 'seminar'],
                            ['name' => 'Sərbəst İşlər / Layihə / Təqdimat', 'max_score' => 10, 'type' => 'assignment'],
                            ['name' => 'Kollokvium / Quizlər / Aralıq İmtahan', 'max_score' => 20, 'type' => 'midterm'],
                            ['name' => 'Final İmtahanı', 'max_score' => 50, 'type' => 'final'],
                        ];
                    @endphp
                    @foreach($defaultComponents as $i => $item)
                        <div class="component-row glass-card" style="padding: 1rem; display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 1rem; align-items: center;">
                            <div>
                                <label style="font-size: 0.75rem; color: #94a3b8; display: block; margin-bottom: 0.25rem;">Komponent Adı</label>
                                <input type="text" name="components[{{ $i }}][name]" value="{{ $item['name'] }}" class="form-input" style="width: 100%; background: #0c1829; border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 0.45rem 0.75rem; border-radius: 0.4rem;" required>
                            </div>
                            <div>
                                <label style="font-size: 0.75rem; color: #94a3b8; display: block; margin-bottom: 0.25rem;">Maksimal Bal</label>
                                <input type="number" step="0.5" min="0.5" max="100" name="components[{{ $i }}][max_score]" value="{{ $item['max_score'] }}" oninput="calculateComponentsTotal()" class="form-input comp-max-score" style="width: 100%; background: #0c1829; border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 0.45rem 0.75rem; border-radius: 0.4rem;" required>
                            </div>
                            <div>
                                <label style="font-size: 0.75rem; color: #94a3b8; display: block; margin-bottom: 0.25rem;">Növü</label>
                                <select name="components[{{ $i }}][type]" style="width: 100%; background: #0c1829; border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 0.45rem 0.75rem; border-radius: 0.4rem;">
                                    <option value="attendance" {{ $item['type'] === 'attendance' ? 'selected' : '' }}>Davamiyyət</option>
                                    <option value="seminar" {{ $item['type'] === 'seminar' ? 'selected' : '' }}>Seminar</option>
                                    <option value="assignment" {{ $item['type'] === 'assignment' ? 'selected' : '' }}>Sərbəst İş / Layihə</option>
                                    <option value="midterm" {{ $item['type'] === 'midterm' ? 'selected' : '' }}>Kollokvium / Aralıq</option>
                                    <option value="final" {{ $item['type'] === 'final' ? 'selected' : '' }}>Final İmtahanı</option>
                                    <option value="custom">Xüsusi</option>
                                </select>
                            </div>
                            <div style="padding-top: 1.2rem;">
                                <button type="button" onclick="removeComponentRow(this)" class="btn btn-outline" style="padding: 0.45rem 0.75rem; color: #f87171; border-color: rgba(239, 68, 68, 0.3);" title="Sil">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @else
                    @foreach($components as $i => $comp)
                        <div class="component-row glass-card" style="padding: 1rem; display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 1rem; align-items: center;">
                            <input type="hidden" name="components[{{ $i }}][id]" value="{{ $comp->id }}">
                            <div>
                                <label style="font-size: 0.75rem; color: #94a3b8; display: block; margin-bottom: 0.25rem;">Komponent Adı</label>
                                <input type="text" name="components[{{ $i }}][name]" value="{{ $comp->name }}" class="form-input" style="width: 100%; background: #0c1829; border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 0.45rem 0.75rem; border-radius: 0.4rem;" required>
                            </div>
                            <div>
                                <label style="font-size: 0.75rem; color: #94a3b8; display: block; margin-bottom: 0.25rem;">Maksimal Bal</label>
                                <input type="number" step="0.5" min="0.5" max="100" name="components[{{ $i }}][max_score]" value="{{ (float)$comp->max_score }}" oninput="calculateComponentsTotal()" class="form-input comp-max-score" style="width: 100%; background: #0c1829; border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 0.45rem 0.75rem; border-radius: 0.4rem;" required>
                            </div>
                            <div>
                                <label style="font-size: 0.75rem; color: #94a3b8; display: block; margin-bottom: 0.25rem;">Növü</label>
                                <select name="components[{{ $i }}][type]" style="width: 100%; background: #0c1829; border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 0.45rem 0.75rem; border-radius: 0.4rem;">
                                    <option value="attendance" {{ $comp->type === 'attendance' ? 'selected' : '' }}>Davamiyyət</option>
                                    <option value="seminar" {{ $comp->type === 'seminar' ? 'selected' : '' }}>Seminar</option>
                                    <option value="assignment" {{ $comp->type === 'assignment' ? 'selected' : '' }}>Sərbəst İş / Layihə</option>
                                    <option value="midterm" {{ $comp->type === 'midterm' ? 'selected' : '' }}>Kollokvium / Aralıq</option>
                                    <option value="final" {{ $comp->type === 'final' ? 'selected' : '' }}>Final İmtahanı</option>
                                    <option value="custom" {{ $comp->type === 'custom' ? 'selected' : '' }}>Xüsusi</option>
                                </select>
                            </div>
                            <div style="padding-top: 1.2rem;">
                                <button type="button" onclick="removeComponentRow(this)" class="btn btn-outline" style="padding: 0.45rem 0.75rem; color: #f87171; border-color: rgba(239, 68, 68, 0.3);" title="Sil">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Dynamic Buttons & Total Score Counter -->
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 1.25rem;">
                <button type="button" onclick="addComponentRow()" class="btn btn-outline" style="font-size: 0.85rem;">
                    <i class="fa-solid fa-plus"></i> Yeni Bal Komponenti Əlavə Et
                </button>

                <div style="display: flex; align-items: center; gap: 1.5rem;">
                    <div style="font-size: 0.9rem; color: #cbd5e1;">
                        Ümumi Maksimal Bal Cəmi: 
                        <strong id="total-max-display" style="font-size: 1.2rem; color: #34d399;">100</strong> / 100 bal
                    </div>

                    <button type="submit" class="btn btn-primary" style="font-size: 0.9rem; padding: 0.65rem 1.5rem;">
                        <i class="fa-solid fa-floppy-disk"></i> Bal Bölgüsünü Yadda Saxla
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- PART 2: STUDENT ELECTRONIC GRADEBOOK JOURNAL (TƏLƏBƏLƏRƏ BAL TƏYİNİ) -->
    <div class="glass-panel" style="padding: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
            <div>
                <h3 class="font-heading" style="font-size: 1.35rem; color: #fff; display: flex; align-items: center; gap: 0.6rem;">
                    <i class="fa-solid fa-clipboard-user text-emerald-400"></i> Elektron Qiymətləndirmə Jurnalı (Tələbə Balları)
                </h3>
                <p style="font-size: 0.85rem; color: #94a3b8; margin-top: 0.25rem;">
                    Təyin edilmiş hər komponent üzrə tələbələrin ballarını daxil edin. Yekun bal və A/B/C/D/F hərfi dərhal hesablanır.
                </p>
            </div>

            <div>
                <button form="grades-matrix-form" type="submit" class="btn btn-primary" style="font-size: 0.88rem; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.35); background: linear-gradient(135deg, #10b981, #059669);">
                    <i class="fa-solid fa-check-double"></i> Bütün Balları Yadda Saxla
                </button>
            </div>
        </div>

        @if($section->enrollments->isEmpty())
            <div style="text-align: center; padding: 3rem 1rem; color: #94a3b8;">
                <i class="fa-solid fa-user-graduate text-4xl mb-3 text-slate-500"></i>
                <div style="font-size: 1.1rem; color: #fff; font-weight: 600;">Bu qrupda hələ qeydiyyatdan keçmiş tələbə yoxdur.</div>
            </div>
        @else
            <form action="{{ route('portal.grading.scores.save', $section->id) }}" method="POST" id="grades-matrix-form">
                @csrf

                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem;">
                        <thead>
                            <tr style="color: #94a3b8; border-bottom: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.2);">
                                <th style="padding: 0.85rem 1rem;">TƏLƏBƏ</th>
                                <th style="padding: 0.85rem 0.75rem;">ID</th>
                                @foreach($components as $comp)
                                    <th style="padding: 0.85rem 0.75rem; text-align: center; white-space: nowrap;">
                                        <div>{{ $comp->name }}</div>
                                        <span class="badge badge-primary" style="font-size: 0.7rem; margin-top: 0.2rem;">Maks: {{ (float)$comp->max_score }}</span>
                                    </th>
                                @endforeach
                                <th style="padding: 0.85rem 1rem; text-align: center;">YEKUN BAL</th>
                                <th style="padding: 0.85rem 1rem; text-align: center;">HƏRFİ (4.0)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($section->enrollments as $enr)
                                @php
                                    $studentGradeItems = $enr->gradeItems->keyBy('grade_component_id');
                                @endphp
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);" id="row-student-{{ $enr->id }}">
                                    <!-- Student Name -->
                                    <td style="padding: 0.85rem 1rem; font-weight: 600; color: #fff;">
                                        {{ $enr->student->name }}
                                    </td>

                                    <!-- Student ID -->
                                    <td class="font-mono" style="padding: 0.85rem 0.75rem; color: #38bdf8;">
                                        {{ $enr->student->student_id }}
                                    </td>

                                    <!-- Input for each component -->
                                    @foreach($components as $comp)
                                        @php
                                            $item = $studentGradeItems->get($comp->id);
                                            $score = $item ? (float)$item->score : 0;
                                        @endphp
                                        <td style="padding: 0.85rem 0.75rem; text-align: center;">
                                            <input type="number" step="0.5" min="0" max="{{ (float)$comp->max_score }}" 
                                                   name="grades[{{ $enr->id }}][{{ $comp->id }}]" 
                                                   value="{{ $score }}" 
                                                   data-enrollment="{{ $enr->id }}"
                                                   data-max="{{ (float)$comp->max_score }}"
                                                   oninput="recalcStudentLiveTotal({{ $enr->id }})"
                                                   class="form-input score-input-{{ $enr->id }}" 
                                                   style="width: 75px; text-align: center; background: #0c1829; border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 0.35rem 0.5rem; border-radius: 0.4rem; font-weight: 700;" required>
                                        </td>
                                    @endforeach

                                    <!-- Total Score -->
                                    <td style="padding: 0.85rem 1rem; text-align: center;">
                                        <span id="total-val-{{ $enr->id }}" class="font-heading" style="font-size: 1.15rem; color: #38bdf8;">
                                            {{ (float)$enr->total_score }}
                                        </span>
                                    </td>

                                    <!-- Letter Grade -->
                                    <td style="padding: 0.85rem 1rem; text-align: center;">
                                        <span id="letter-val-{{ $enr->id }}" class="badge {{ $enr->letter_grade === 'A' ? 'badge-success' : ($enr->letter_grade === 'F' ? 'badge-danger' : 'badge-primary') }}" style="font-size: 0.85rem;">
                                            {{ $enr->letter_grade }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-size: 0.95rem; background: linear-gradient(135deg, #10b981, #059669);">
                        <i class="fa-solid fa-floppy-disk"></i> Bütün Balları Yadda Saxla və Təsdiqlə
                    </button>
                </div>
            </form>
        @endif
    </div>

@endsection

@section('scripts')
<script>
    function calculateComponentsTotal() {
        let inputs = document.querySelectorAll('.comp-max-score');
        let sum = 0;
        inputs.forEach(inp => {
            let val = parseFloat(inp.value) || 0;
            sum += val;
        });

        let display = document.getElementById('total-max-display');
        if (display) {
            display.innerText = sum.toFixed(1);
            if (sum === 100) {
                display.style.color = '#34d399';
            } else {
                display.style.color = '#f87171';
            }
        }
    }

    function removeComponentRow(btn) {
        let rows = document.querySelectorAll('.component-row');
        if (rows.length <= 1) {
            alert('Ən azı 1 bal komponenti qalmalıdır!');
            return;
        }
        btn.closest('.component-row').remove();
        calculateComponentsTotal();
    }

    function addComponentRow() {
        let container = document.getElementById('components-container');
        let index = new Date().getTime();
        let div = document.createElement('div');
        div.className = 'component-row glass-card';
        div.style = 'padding: 1rem; display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 1rem; align-items: center;';
        div.innerHTML = `
            <div>
                <label style="font-size: 0.75rem; color: #94a3b8; display: block; margin-bottom: 0.25rem;">Komponent Adı</label>
                <input type="text" name="components[${index}][name]" value="Yeni Qiymətləndirmə" class="form-input" style="width: 100%; background: #0c1829; border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 0.45rem 0.75rem; border-radius: 0.4rem;" required>
            </div>
            <div>
                <label style="font-size: 0.75rem; color: #94a3b8; display: block; margin-bottom: 0.25rem;">Maksimal Bal</label>
                <input type="number" step="0.5" min="0.5" max="100" name="components[${index}][max_score]" value="10" oninput="calculateComponentsTotal()" class="form-input comp-max-score" style="width: 100%; background: #0c1829; border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 0.45rem 0.75rem; border-radius: 0.4rem;" required>
            </div>
            <div>
                <label style="font-size: 0.75rem; color: #94a3b8; display: block; margin-bottom: 0.25rem;">Növü</label>
                <select name="components[${index}][type]" style="width: 100%; background: #0c1829; border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 0.45rem 0.75rem; border-radius: 0.4rem;">
                    <option value="custom">Xüsusi</option>
                    <option value="seminar">Seminar</option>
                    <option value="assignment">Sərbəst İş</option>
                    <option value="midterm">Kollokvium</option>
                    <option value="final">Final İmtahanı</option>
                    <option value="attendance">Davamiyyət</option>
                </select>
            </div>
            <div style="padding-top: 1.2rem;">
                <button type="button" onclick="removeComponentRow(this)" class="btn btn-outline" style="padding: 0.45rem 0.75rem; color: #f87171; border-color: rgba(239, 68, 68, 0.3);" title="Sil">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </div>
        `;
        container.appendChild(div);
        calculateComponentsTotal();
    }

    function recalcStudentLiveTotal(enrollmentId) {
        let inputs = document.querySelectorAll('.score-input-' + enrollmentId);
        let sum = 0;
        inputs.forEach(inp => {
            let max = parseFloat(inp.getAttribute('data-max')) || 100;
            let val = parseFloat(inp.value) || 0;
            if (val > max) {
                val = max;
                inp.value = max;
            }
            sum += val;
        });

        let totalElem = document.getElementById('total-val-' + enrollmentId);
        if (totalElem) {
            totalElem.innerText = sum.toFixed(1);
        }

        let letterElem = document.getElementById('letter-val-' + enrollmentId);
        if (letterElem) {
            let letter = 'F';
            let badgeClass = 'badge-danger';
            if (sum >= 91) { letter = 'A'; badgeClass = 'badge-success'; }
            else if (sum >= 81) { letter = 'B'; badgeClass = 'badge-primary'; }
            else if (sum >= 71) { letter = 'C'; badgeClass = 'badge-primary'; }
            else if (sum >= 61) { letter = 'D'; badgeClass = 'badge-warning'; }
            else if (sum >= 51) { letter = 'E'; badgeClass = 'badge-warning'; }

            letterElem.innerText = letter;
            letterElem.className = 'badge ' + badgeClass;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        calculateComponentsTotal();
    });
</script>
@endsection
