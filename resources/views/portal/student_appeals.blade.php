@extends('layouts.portal')

@section('title', 'Tələbə Müraciətləri və Ərizələri — Xəzər Universiteti')
@section('header_title', 'Müraciətlər və İmtahan Ərizələri')

@section('content')

    <!-- Top Info Banner -->
    <div class="glass-panel" style="padding: 2rem; margin-bottom: 2rem; background: linear-gradient(135deg, rgba(6, 17, 36, 0.9), rgba(12, 24, 44, 0.95));">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
            <div>
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                    <span class="badge badge-student"><i class="fa-solid fa-graduation-cap"></i> {{ $user->name }}</span>
                    <span class="badge badge-warning"><i class="fa-solid fa-file-signature"></i> Elektron Ərizələr</span>
                </div>
                <h2 class="font-heading" style="font-size: 1.8rem; color: #fff; margin-bottom: 0.25rem;">Tələbə Müraciət və Apellyasiya Mərkəzi</h2>
                <p style="color: #94a3b8; font-size: 0.9rem;">
                    İmtahan nəticələrinə etiraz (apellyasiya), tamamlama/cərimə imtahanı və dekanlığa rəsmi ərizələrin göndərilməsi və statusunun izlənməsi.
                </p>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs for the 4 Types -->
    <div style="display: flex; gap: 0.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 2rem; overflow-x: auto; padding-bottom: 0.5rem;">
        <a href="{{ route('portal.appeals.index', ['type' => 'grade_objection']) }}" 
           class="btn {{ $activeTab === 'grade_objection' ? 'btn-primary' : 'btn-outline' }}" style="font-size: 0.88rem; white-space: nowrap;">
            <i class="fa-solid fa-scale-balanced"></i> İmtahan nəticəsinə etiraz et
        </a>
        <a href="{{ route('portal.appeals.index', ['type' => 'makeup_exam']) }}" 
           class="btn {{ $activeTab === 'makeup_exam' ? 'btn-primary' : 'btn-outline' }}" style="font-size: 0.88rem; white-space: nowrap;">
            <i class="fa-solid fa-file-circle-check"></i> Tamamlama imtahanına müraciət
        </a>
        <a href="{{ route('portal.appeals.index', ['type' => 'penalty_exam']) }}" 
           class="btn {{ $activeTab === 'penalty_exam' ? 'btn-primary' : 'btn-outline' }}" style="font-size: 0.88rem; white-space: nowrap;">
            <i class="fa-solid fa-file-invoice-dollar"></i> Cərimə imtahanına müraciət
        </a>
        <a href="{{ route('portal.appeals.index', ['type' => 'dean_petition']) }}" 
           class="btn {{ $activeTab === 'dean_petition' ? 'btn-primary' : 'btn-outline' }}" style="font-size: 0.88rem; white-space: nowrap;">
            <i class="fa-solid fa-building-columns"></i> Dekanlığa müraciət
        </a>
    </div>

    <div style="display: grid; grid-template-columns: 1.1fr 1fr; gap: 2rem; align-items: start;">
        
        <!-- Submission Form Card -->
        <div class="glass-panel" style="padding: 2rem;">
            <h3 class="font-heading" style="font-size: 1.3rem; color: #fff; margin-bottom: 0.35rem; display: flex; align-items: center; gap: 0.5rem;">
                @if($activeTab === 'grade_objection')
                    <i class="fa-solid fa-scale-balanced text-amber-400"></i> İmtahan Nəticəsinə Etiraz (Apellyasiya Ərizəsi)
                @elseif($activeTab === 'makeup_exam')
                    <i class="fa-solid fa-file-circle-check text-sky-400"></i> Tamamlama İmtahanına Müraciət Ərizəsi
                @elseif($activeTab === 'penalty_exam')
                    <i class="fa-solid fa-file-invoice-dollar text-rose-400"></i> Cərimə İmtahanı Müraciət Ərizəsi
                @else
                    <i class="fa-solid fa-building-columns text-emerald-400"></i> Dekanlığa Müraciət / Ərizə
                @endif
            </h3>
            <p style="color: #94a3b8; font-size: 0.85rem; margin-bottom: 1.5rem;">
                Müraciətiniz avtomatik olaraq qeydiyyata alınacaq və cavablandırıldıqda bildiriş göndəriləcəkdir.
            </p>

            <form action="{{ route('portal.appeals.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="{{ $activeTab }}">

                <!-- Course Selection (if applicable) -->
                @if($activeTab !== 'dean_petition')
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.4rem;">
                            Əlaqəli Fənni Seçin <span style="color: #f87171;">*</span>
                        </label>
                        <select name="course_section_id" class="form-input" style="width: 100%; background: #0c1829; border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 0.65rem 1rem; border-radius: 0.5rem;" required>
                            <option value="">-- Fənni seçin --</option>
                            @foreach($myEnrollments as $enr)
                                <option value="{{ $enr->courseSection?->id }}" {{ request('course_section_id') == $enr->courseSection?->id ? 'selected' : '' }}>
                                    {{ $enr->courseSection?->course?->code }} - {{ $enr->courseSection?->course?->name }} (Müəllim: {{ $enr->courseSection?->teacher?->name ?? 'Professor' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Subject Title -->
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.4rem;">
                        Müraciətin Mövzusu / Başlığı <span style="color: #f87171;">*</span>
                    </label>
                    <input type="text" name="subject" value="{{ old('subject', $activeTab === 'grade_objection' ? 'Final İmtahanı Balına Etiraz' : ($activeTab === 'makeup_exam' ? 'Tamamlama İmtahanı üçün Ərizə' : ($activeTab === 'penalty_exam' ? 'Cərimə İmtahanında İştirak Müraciəti' : 'Akademik Arayış / Ərizə'))) }}" 
                           class="form-input" style="width: 100%; background: #0c1829; border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 0.65rem 1rem; border-radius: 0.5rem;" required>
                </div>

                <!-- Reason & Explanation -->
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.4rem;">
                        Ətraflı Səbəb və Əsaslandırma <span style="color: #f87171;">*</span>
                    </label>
                    <textarea name="reason" rows="4" class="form-input" 
                              placeholder="Müraciətinizin səbəbini, imtahan sualı və ya qeydlərinizi aydın qeyd edin..." 
                              style="width: 100%; background: #0c1829; border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 0.65rem 1rem; border-radius: 0.5rem; resize: vertical;" required>{{ old('reason') }}</textarea>
                </div>

                <!-- File Attachment -->
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.4rem;">
                        Əlavə Sənəd və ya Şəkil (Könüllü, maks. 5MB)
                    </label>
                    <input type="file" name="attachment" accept=".pdf,.png,.jpg,.jpeg,.doc,.docx"
                           style="width: 100%; font-size: 0.85rem; color: #94a3b8; background: #0c1829; border: 1px solid rgba(255,255,255,0.1); padding: 0.5rem; border-radius: 0.5rem;">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem; font-size: 0.95rem;">
                    <i class="fa-solid fa-paper-plane"></i> Müraciəti Təsdiqlə və Göndər
                </button>
            </form>
        </div>

        <!-- Appeals History & Live Status -->
        <div class="glass-panel" style="padding: 2rem;">
            <h3 class="font-heading" style="font-size: 1.3rem; color: #fff; margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: space-between;">
                <span><i class="fa-solid fa-clock-rotate-left text-sky-400"></i> Müraciətlərimin Tarixçəsi</span>
                <span class="badge badge-primary">{{ $myAppeals->count() }} Ərizə</span>
            </h3>

            @if($myAppeals->isEmpty())
                <div style="text-align: center; padding: 3rem 1rem; color: #64748b;">
                    <i class="fa-solid fa-inbox text-3xl mb-2 opacity-50 block"></i>
                    Hələlik heç bir müraciət göndərməmisiniz.
                </div>
            @else
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @foreach($myAppeals as $app)
                        <div class="glass-card" style="padding: 1.25rem; border-left: 3px solid {{ $app->status === 'approved' ? '#34d399' : ($app->status === 'rejected' ? '#f87171' : '#fbbf24') }};">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.4rem;">
                                <span class="badge {{ $app->status_badge }}" style="font-size: 0.75rem;">
                                    {{ $app->status_label }}
                                </span>
                                <span style="font-size: 0.75rem; color: #94a3b8;">
                                    <i class="fa-regular fa-clock"></i> {{ $app->submitted_at?->format('d.m.Y H:i') }}
                                </span>
                            </div>

                            <div style="font-weight: 700; color: #fff; font-size: 0.95rem; margin-bottom: 0.25rem;">
                                {{ $app->subject }}
                            </div>

                            <div style="font-size: 0.82rem; color: #38bdf8; margin-bottom: 0.5rem;">
                                {{ $app->type_name }}
                                @if($app->courseSection)
                                    • <span style="color: #cbd5e1;">{{ $app->courseSection->course->name }}</span>
                                @endif
                            </div>

                            <p style="font-size: 0.82rem; color: #94a3b8; line-height: 1.4; background: rgba(0,0,0,0.2); padding: 0.6rem; border-radius: 0.4rem; margin-bottom: 0.5rem;">
                                {{ $app->reason }}
                            </p>

                            @if($app->reviewer_notes)
                                <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.25); border-radius: 0.4rem; padding: 0.6rem; font-size: 0.82rem; color: #a7f3d0;">
                                    <strong><i class="fa-solid fa-comment-dots"></i> Dekanlıq / Müəllim Cavabı:</strong>
                                    <div style="margin-top: 0.2rem;">{{ $app->reviewer_notes }}</div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

@endsection
