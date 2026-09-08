@extends('layouts.portal')

@section('title', "{$section->course->name} — LMS Otağı")
@section('header_title', "LMS: {$section->course->name} ({$section->course->code})")

@section('content')

    <!-- Course Header Banner -->
    <div class="glass-panel" style="padding: 2rem; margin-bottom: 2rem; background: linear-gradient(135deg, rgba(15, 23, 42, 0.9), rgba(0, 43, 73, 0.7)); border-color: rgba(0, 163, 224, 0.35);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
            <div>
                <div style="display: flex; align-items: center; gap: 0.65rem; margin-bottom: 0.5rem;">
                    <span class="badge badge-primary font-mono" style="font-size: 0.85rem;">{{ $section->course->code }}</span>
                    <span class="badge badge-success">{{ $section->semester_term }}</span>
                    <span style="color: #38bdf8; font-size: 0.85rem; font-weight: 600;">{{ $section->course->credits }} ECTS</span>
                </div>

                <h2 class="font-heading" style="font-size: 1.8rem; color: #fff; margin-bottom: 0.4rem;">
                    {{ $section->course->name }}
                </h2>

                <div style="font-size: 0.9rem; color: #cbd5e1; display: flex; gap: 1.5rem; flex-wrap: wrap;">
                    <span><i class="fa-solid fa-chalkboard-user text-sky-400"></i> Müəllim: <strong>{{ $section->teacher->name ?? 'Professor' }}</strong></span>
                    <span><i class="fa-solid fa-location-dot text-amber-400"></i> {{ $section->room_number }}</span>
                    <span><i class="fa-regular fa-clock text-emerald-400"></i> {{ $section->day_of_week }} ({{ $section->start_time }}-{{ $section->end_time }})</span>
                </div>
            </div>

            @if($isTeacherOrAdmin)
                <div style="display: flex; gap: 0.75rem;">
                    <button onclick="document.getElementById('modal-add-assignment').style.display='flex'" class="btn gradient-btn" style="padding: 0.5rem 1rem; font-size: 0.85rem;">
                        <i class="fa-solid fa-plus"></i> Tapşırıq Əlavə Et
                    </button>
                    <button onclick="document.getElementById('modal-add-material').style.display='flex'" class="btn btn-outline" style="padding: 0.5rem 1rem; font-size: 0.85rem;">
                        <i class="fa-solid fa-upload"></i> Material Yüklə
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Main Grid: Left Tabs (Assignments, Materials, Announcements), Right Side Info -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        
        <!-- Left Column -->
        <div>
            
            <!-- Section 1: Assignments -->
            <div class="glass-panel" style="padding: 1.75rem; margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                    <h3 class="font-heading" style="font-size: 1.25rem; color: #fff;">
                        <i class="fa-solid fa-list-check text-sky-400"></i> Fənn Tapşırıqları və Layihələr
                    </h3>
                    <span class="badge badge-primary">{{ $section->assignments->count() }} Tapşırıq</span>
                </div>

                @forelse($section->assignments as $assign)
                    @php
                        $mySub = $mySubmissions[$assign->id] ?? null;
                    @endphp
                    <div class="glass-card" style="padding: 1.25rem; margin-bottom: 1rem; border-left: 4px solid {{ $mySub ? '#10b981' : '#f59e0b' }};">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                            <div>
                                <h4 class="font-heading" style="font-size: 1.1rem; color: #fff; margin-bottom: 0.2rem;">{{ $assign->title }}</h4>
                                <div style="font-size: 0.75rem; color: #94a3b8;">
                                    Son təqdimat tarixi: <strong style="color: #fca5a5;">{{ $assign->due_date->format('d.m.Y H:i') }}</strong> • Maksimum bal: <strong style="color: #38bdf8;">{{ $assign->max_score }} bal</strong>
                                </div>
                            </div>

                            @if(!$isTeacherOrAdmin)
                                <div>
                                    @if($mySub)
                                        @if($mySub->score !== null)
                                            <span class="badge badge-success" style="font-size: 0.85rem;">
                                                Qiymət: {{ $mySub->score }} / {{ $assign->max_score }}
                                            </span>
                                        @else
                                            <span class="badge badge-warning">Təqdim Edildi (Yoxlanılır)</span>
                                        @endif
                                    @else
                                        <span class="badge badge-danger">Təqdim Edilməyib</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <p style="font-size: 0.85rem; color: #cbd5e1; margin-bottom: 1rem; line-height: 1.5;">
                            {{ $assign->description }}
                        </p>

                        <!-- Student Submission Form -->
                        @if(!$isTeacherOrAdmin)
                            <div style="background: rgba(255,255,255,0.02); padding: 1rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.06);">
                                @if($mySub && $mySub->feedback)
                                    <div style="margin-bottom: 0.75rem; background: rgba(56, 189, 248, 0.1); padding: 0.65rem; border-radius: 0.4rem; font-size: 0.8rem; color: #bae6fd;">
                                        <strong>Müəllim Rəyi:</strong> {{ $mySub->feedback }}
                                    </div>
                                @endif

                                <form action="{{ route('portal.lms.assignment.submit', $assign->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 0.75rem; align-items: flex-end;">
                                        <div>
                                            <label style="font-size: 0.75rem; color: #94a3b8; display: block; margin-bottom: 0.25rem;">Şərh / Həll Mətni</label>
                                            <input type="text" name="submission_text" value="{{ $mySub->submission_text ?? '' }}" placeholder="Tapşırıq izahı..." class="form-input" style="padding: 0.4rem 0.6rem; font-size: 0.85rem;">
                                        </div>
                                        <div>
                                            <label style="font-size: 0.75rem; color: #94a3b8; display: block; margin-bottom: 0.25rem;">Fayl (PDF, DOCX, ZIP, Kod)</label>
                                            <input type="file" name="file" class="form-input" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                                        </div>
                                        <div>
                                            <button type="submit" class="btn btn-primary" style="padding: 0.45rem 0.85rem; font-size: 0.8rem;">
                                                <i class="fa-solid fa-paper-plane"></i> {{ $mySub ? 'Yenilə' : 'Göndər' }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @else
                            <!-- Teacher View: Submissions list -->
                            <div style="margin-top: 1rem; border-top: 1px solid rgba(255,255,255,0.06); padding-top: 0.75rem;">
                                <div style="font-size: 0.8rem; font-weight: 700; color: #cbd5e1; margin-bottom: 0.5rem;">
                                    Tələbə Təqdimatları ({{ $assign->submissions->count() }}):
                                </div>
                                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                    @foreach($assign->submissions as $sub)
                                        <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(0,0,0,0.2); padding: 0.5rem 0.75rem; border-radius: 0.4rem; font-size: 0.8rem;">
                                            <div>
                                                <strong style="color: #fff;">{{ $sub->student->name }}</strong>
                                                <span style="color: #94a3b8; font-size: 0.75rem;">({{ $sub->submitted_at->format('d.m H:i') }})</span>
                                                @if($sub->file_path)
                                                    <a href="{{ asset($sub->file_path) }}" target="_blank" style="color: #38bdf8; margin-left: 0.5rem;"><i class="fa-solid fa-paperclip"></i> Fayla Bax</a>
                                                @endif
                                            </div>

                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                @if($sub->score !== null)
                                                    <span style="color: #34d399; font-weight: 700;">{{ $sub->score }} / {{ $assign->max_score }}</span>
                                                @endif
                                                <button onclick="openGradeModal({{ $sub->id }}, '{{ addslashes($sub->student->name) }}', {{ $assign->max_score }}, '{{ $sub->score ?? '' }}', '{{ addslashes($sub->feedback ?? '') }}')" class="btn btn-outline" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;">
                                                    Qiymətləndir
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>
                @empty
                    <div style="text-align: center; color: #94a3b8; padding: 1.5rem;">Bu fənn üçün hələ tapşırıq əlavə edilməyib.</div>
                @endforelse
            </div>

            <!-- Section 2: Materials & Syllabus -->
            <div class="glass-panel" style="padding: 1.75rem;">
                <h3 class="font-heading" style="font-size: 1.25rem; color: #fff; margin-bottom: 1.25rem;">
                    <i class="fa-solid fa-book-open text-purple-400"></i> Fənn Materialları, Mühazirə və Slaydlar
                </h3>

                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @forelse($section->materials as $mat)
                        <div class="glass-card" style="padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 36px; height: 36px; background: rgba(168, 85, 247, 0.2); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; color: #c084fc;">
                                    @if($mat->type === 'video') <i class="fa-solid fa-video"></i>
                                    @elseif($mat->type === 'link') <i class="fa-solid fa-link"></i>
                                    @else <i class="fa-solid fa-file-pdf"></i>
                                    @endif
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: #fff; font-size: 0.95rem;">{{ $mat->title }}</div>
                                    <div style="font-size: 0.75rem; color: #94a3b8;">Format: {{ strtoupper($mat->type) }}</div>
                                </div>
                            </div>

                            <a href="{{ asset($mat->file_path_or_url) }}" target="_blank" class="btn btn-outline" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; color: #c084fc;">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i> Materialı Aç
                            </a>
                        </div>
                    @empty
                        <div style="text-align: center; color: #94a3b8; padding: 1.5rem;">Material yüklənməyib.</div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Right Column: Announcements & Attendance Taker for Teacher -->
        <div>
            
            <!-- Announcements -->
            <div class="glass-panel" style="padding: 1.5rem; margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3 class="font-heading" style="font-size: 1.15rem; color: #fff;"><i class="fa-solid fa-bullhorn text-amber-400"></i> Fənn Elanları</h3>
                    @if($isTeacherOrAdmin)
                        <button onclick="document.getElementById('modal-add-announcement').style.display='flex'" class="btn btn-outline" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">+ Elan</button>
                    @endif
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    @forelse($section->announcements as $ann)
                        <div style="background: rgba(255,255,255,0.02); border-left: 3px solid #fbbf24; padding: 0.85rem; border-radius: 0.4rem;">
                            <div style="font-weight: 700; color: #fff; font-size: 0.9rem; margin-bottom: 0.2rem;">{{ $ann->title }}</div>
                            <p style="font-size: 0.8rem; color: #cbd5e1; line-height: 1.4; margin-bottom: 0.4rem;">{{ $ann->content }}</p>
                            <div style="font-size: 0.7rem; color: #94a3b8;">{{ $ann->published_at->format('d.m.Y H:i') }}</div>
                        </div>
                    @empty
                        <div style="font-size: 0.85rem; color: #94a3b8; text-align: center;">Elan yoxdur.</div>
                    @endforelse
                </div>
            </div>

            <!-- Per-Session Attendance Taker for Teacher -->
            @if($isTeacherOrAdmin)
                <div class="glass-panel" style="padding: 1.5rem;">
                    <h3 class="font-heading" style="font-size: 1.15rem; color: #fff; margin-bottom: 1rem;">
                        <i class="fa-solid fa-user-check text-emerald-400"></i> Günlük Davamiyyət Qeydiyyatı
                    </h3>

                    <form action="{{ route('portal.attendance.store', $section->id) }}" method="POST">
                        @csrf
                        <div style="margin-bottom: 1rem;">
                            <label style="font-size: 0.75rem; color: #94a3b8; display: block; margin-bottom: 0.25rem;">Dərs Tarixi</label>
                            <input type="date" name="session_date" value="{{ date('Y-m-d') }}" required class="form-input" style="padding: 0.35rem 0.5rem; font-size: 0.85rem;">
                        </div>

                        <div style="max-height: 250px; overflow-y: auto; margin-bottom: 1rem; display: flex; flex-direction: column; gap: 0.5rem;">
                            @foreach($section->enrollments as $enr)
                                <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.02); padding: 0.4rem 0.6rem; border-radius: 0.4rem; font-size: 0.8rem;">
                                    <div style="color: #fff; font-weight: 600;">{{ $enr->student->name }}</div>
                                    <select name="attendance[{{ $enr->id }}]" class="form-select" style="font-size: 0.75rem; padding: 0.2rem 0.4rem; width: 100px;">
                                        <option value="present">İştirak</option>
                                        <option value="absent">Q/B</option>
                                        <option value="late">Gecikmə</option>
                                        <option value="excused">Üzrlü</option>
                                    </select>
                                </div>
                            @endforeach
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.5rem; font-size: 0.85rem;">
                            Davamiyyəti Saxla
                        </button>
                    </form>
                </div>
            @endif

        </div>

    </div>

    <!-- MODAL 1: Create Assignment -->
    <div id="modal-add-assignment" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
        <div class="glass-panel" style="background: #091322; border: 1px solid rgba(56, 189, 248, 0.4); border-radius: 1rem; width: 100%; max-width: 550px; padding: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <h3 class="font-heading" style="font-size: 1.3rem; color: #fff;"><i class="fa-solid fa-list-check text-sky-400"></i> Yeni Tapşırıq Əlavə Et</h3>
                <button onclick="document.getElementById('modal-add-assignment').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.2rem; cursor: pointer;">&times;</button>
            </div>

            <form action="{{ route('portal.lms.assignment.create', $section->id) }}" method="POST">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Tapşırığın Başlığı</label>
                    <input type="text" name="title" required placeholder="Məs: Laboratoriya İşi №3" class="form-input">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Son Təhvil Tarixi & Saat</label>
                        <input type="datetime-local" name="due_date" required class="form-input">
                    </div>
                    <div>
                        <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Maksimum Bal</label>
                        <input type="number" step="0.5" name="max_score" value="10" required class="form-input">
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Tapşırığın İzahı və Tələblər</label>
                    <textarea name="description" rows="4" class="form-input" placeholder="Tapşırığın detallı təlimatları..."></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" onclick="document.getElementById('modal-add-assignment').style.display='none'" class="btn btn-outline" style="padding: 0.5rem 1rem;">Ləğv et</button>
                    <button type="submit" class="btn gradient-btn" style="padding: 0.5rem 1.25rem;">Tapşırığı Paylaş</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: Add Material -->
    <div id="modal-add-material" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
        <div class="glass-panel" style="background: #091322; border: 1px solid rgba(168, 85, 247, 0.4); border-radius: 1rem; width: 100%; max-width: 500px; padding: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <h3 class="font-heading" style="font-size: 1.3rem; color: #fff;"><i class="fa-solid fa-file-arrow-up text-purple-400"></i> Fənn Materialı Yüklə</h3>
                <button onclick="document.getElementById('modal-add-material').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.2rem; cursor: pointer;">&times;</button>
            </div>

            <form action="{{ route('portal.lms.material.add', $section->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Materialın Adı</label>
                    <input type="text" name="title" required placeholder="Məs: Mühazirə 4 — Qraflar Slaydı" class="form-input">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Material Tipi</label>
                    <select name="type" class="form-select">
                        <option value="file">Sənəd / PDF / Slayd</option>
                        <option value="link">Veb Keçid (Link)</option>
                        <option value="video">Video Dərs</option>
                        <option value="syllabus">Sillabus</option>
                    </select>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Fayl Yüklə</label>
                    <input type="file" name="file" class="form-input">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">və ya Keçid Linki (URL)</label>
                    <input type="url" name="url" placeholder="https://..." class="form-input">
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" onclick="document.getElementById('modal-add-material').style.display='none'" class="btn btn-outline" style="padding: 0.5rem 1rem;">Ləğv et</button>
                    <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1.25rem;">Yüklə</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: Grade Submission (Teacher) -->
    <div id="modal-grade-submission" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
        <div class="glass-panel" style="background: #091322; border: 1px solid rgba(52, 211, 153, 0.4); border-radius: 1rem; width: 100%; max-width: 480px; padding: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <h3 class="font-heading" style="font-size: 1.3rem; color: #fff;"><i class="fa-solid fa-clipboard-check text-emerald-400"></i> Tapşırığı Qiymətləndir</h3>
                <button onclick="document.getElementById('modal-grade-submission').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.2rem; cursor: pointer;">&times;</button>
            </div>

            <p style="font-size: 0.85rem; color: #cbd5e1; margin-bottom: 1rem;">
                Tələbə: <strong id="grade-student-name" style="color: #38bdf8;"></strong>
            </p>

            <form id="form-grade-submission" method="POST" action="">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Bal (Maksimum <span id="grade-max-score"></span> bal)</label>
                    <input type="number" step="0.5" min="0" id="grade-input-score" name="score" required class="form-input">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Müəllim Rəyi və Qeydləri</label>
                    <textarea id="grade-input-feedback" name="feedback" rows="3" class="form-input" placeholder="Tələbə üçün rəy..."></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" onclick="document.getElementById('modal-grade-submission').style.display='none'" class="btn btn-outline" style="padding: 0.5rem 1rem;">Bağla</button>
                    <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1.25rem; background: #059669; border-color: #059669;">Qiyməti Təsdiqlə</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 4: Add Announcement -->
    <div id="modal-add-announcement" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
        <div class="glass-panel" style="background: #091322; border: 1px solid rgba(251, 191, 36, 0.4); border-radius: 1rem; width: 100%; max-width: 500px; padding: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <h3 class="font-heading" style="font-size: 1.3rem; color: #fff;"><i class="fa-solid fa-bullhorn text-amber-400"></i> Fənn Qrupuna Elan Paylaş</h3>
                <button onclick="document.getElementById('modal-add-announcement').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.2rem; cursor: pointer;">&times;</button>
            </div>

            <form action="{{ route('portal.lms.announcement.add', $section->id) }}" method="POST">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Elanın Başlığı</label>
                    <input type="text" name="title" required placeholder="Məs: Sabahkı mühazirə otağı dəyişdirildi" class="form-input">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Elanın Mətni</label>
                    <textarea name="content" rows="3" required class="form-input" placeholder="Elan mətni..."></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" onclick="document.getElementById('modal-add-announcement').style.display='none'" class="btn btn-outline" style="padding: 0.5rem 1rem;">Ləğv et</button>
                    <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1.25rem;">Paylaş</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    function openGradeModal(submissionId, studentName, maxScore, currentScore, feedback) {
        document.getElementById('grade-student-name').innerText = studentName;
        document.getElementById('grade-max-score').innerText = maxScore;
        document.getElementById('grade-input-score').max = maxScore;
        document.getElementById('grade-input-score').value = currentScore;
        document.getElementById('grade-input-feedback').value = feedback;
        document.getElementById('form-grade-submission').action = `/portal/lms/submissions/${submissionId}/grade`;
        document.getElementById('modal-grade-submission').style.display = 'flex';
    }
</script>
@endsection
