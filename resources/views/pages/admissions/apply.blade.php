@extends('layouts.app')

@section('title', 'Onlayn Qəbul və Sənəd Qəbulu — Xəzər Universiteti')

@section('content')

    <!-- Header Banner -->
    <div style="background: radial-gradient(circle at 50% 0%, rgba(99, 102, 241, 0.25), transparent 70%); padding: 4rem 0 3rem; border-bottom: 1px solid rgba(255,255,255,0.06); text-align: center;">
        <div class="container">
            <span class="badge badge-accent" style="margin-bottom: 0.75rem;"><i class="fa-solid fa-paper-plane"></i> Elektron Qəbul Portalı</span>
            <h1 class="font-heading" style="font-size: 2.8rem; margin-bottom: 0.75rem;">Universitetə Onlayn Müraciət</h1>
            <p style="color: #94a3b8; font-size: 1.1rem; max-width: 650px; margin: 0 auto;">
                2026/2027-ci tədris ili üçün Bakalavr, Magistratura və Doktorantura proqramlarına qeydiyyatdan keçin.
            </p>
        </div>
    </div>

    <!-- Application Form Container -->
    <div class="container" style="padding: 3.5rem 1.5rem; max-width: 900px;">
        <div class="glass-panel" style="padding: 2.5rem; border-color: rgba(99, 102, 241, 0.3);">
            
            <form action="{{ route('admissions.submit') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Step 1: Program Choice -->
                <div style="margin-bottom: 2.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                        <span style="width: 28px; height: 28px; background: #6366f1; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 700;">1</span>
                        <h3 class="font-heading" style="font-size: 1.3rem;">Tədris Pilləsi və İxtisas Seçimi</h3>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.25rem;">
                        <div>
                            <label style="font-size: 0.85rem; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 0.4rem;">Təhsil Pilləsi *</label>
                            <select name="degree_level" class="form-select" required>
                                <option value="bachelor">Bakalavr (BSc)</option>
                                <option value="master">Magistratura (MSc)</option>
                                <option value="phd">Doktorantura (PhD)</option>
                            </select>
                        </div>

                        <div>
                            <label style="font-size: 0.85rem; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 0.4rem;">Müraciət Edilən İxtisas *</label>
                            <select name="program_id" class="form-select" required>
                                <option value="" disabled {{ !$selectedProgramId ? 'selected' : '' }}>İxtisası seçin...</option>
                                @foreach($programs as $p)
                                    <option value="{{ $p->id }}" {{ $selectedProgramId == $p->id ? 'selected' : '' }}>
                                        [{{ strtoupper($p->degree_level) }}] {{ $p->name }} ({{ $p->department->faculty->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Personal Information -->
                <div style="margin-bottom: 2.5rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.06);">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                        <span style="width: 28px; height: 28px; background: #6366f1; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 700;">2</span>
                        <h3 class="font-heading" style="font-size: 1.3rem;">Şəxsi Məlumatlar</h3>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
                        <div>
                            <label style="font-size: 0.85rem; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 0.4rem;">Adınız *</label>
                            <input type="text" name="first_name" required placeholder="Məs: Kənan" class="form-input">
                        </div>
                        <div>
                            <label style="font-size: 0.85rem; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 0.4rem;">Soyadınız *</label>
                            <input type="text" name="last_name" required placeholder="Məs: Qasımov" class="form-input">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
                        <div>
                            <label style="font-size: 0.85rem; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 0.4rem;">Elektron Poçt (Email) *</label>
                            <input type="email" name="email" required placeholder="example@mail.com" class="form-input">
                        </div>
                        <div>
                            <label style="font-size: 0.85rem; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 0.4rem;">Əlaqə Nömrəsi *</label>
                            <input type="text" name="phone" required placeholder="+994 50 123 45 67" class="form-input">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.25rem;">
                        <div>
                            <label style="font-size: 0.85rem; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 0.4rem;">Ş/V FİN Kodu</label>
                            <input type="text" name="passport_fin" placeholder="Məs: 7ABC123" class="form-input">
                        </div>
                        <div>
                            <label style="font-size: 0.85rem; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 0.4rem;">Doğum Tarixi</label>
                            <input type="date" name="birth_date" class="form-input">
                        </div>
                        <div>
                            <label style="font-size: 0.85rem; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 0.4rem;">Cins</label>
                            <select name="gender" class="form-select">
                                <option value="Kişi">Kişi</option>
                                <option value="Qadın">Qadın</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Academic Background & Scores -->
                <div style="margin-bottom: 2.5rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.06);">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                        <span style="width: 28px; height: 28px; background: #6366f1; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 700;">3</span>
                        <h3 class="font-heading" style="font-size: 1.3rem;">Akademik Göstəricilər & İmtahan Balları</h3>
                    </div>

                    <div style="display: grid; grid-template-columns: 1.5fr 1fr 1fr; gap: 1.25rem;">
                        <div>
                            <label style="font-size: 0.85rem; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 0.4rem;">İmtahan Növü</label>
                            <select name="exam_type" class="form-select">
                                <option value="DİM">DİM Qəbul İmtahanı (Milli)</option>
                                <option value="SAT">SAT (Beynəlxalq)</option>
                                <option value="IELTS/TOEFL">IELTS / TOEFL</option>
                                <option value="Olimpiada">Respublika / Beynəlxalq Olimpiada Qalibi</option>
                            </select>
                        </div>
                        <div>
                            <label style="font-size: 0.85rem; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 0.4rem;">Toplanmış Bal</label>
                            <input type="number" name="exam_score" placeholder="Məs: 620" class="form-input">
                        </div>
                        <div>
                            <label style="font-size: 0.85rem; font-weight: 600; color: #cbd5e1; display: block; margin-bottom: 0.4rem;">Attestat / GPA Balı</label>
                            <input type="number" step="0.01" name="high_school_gpa" placeholder="Məs: 4.85" class="form-input">
                        </div>
                    </div>
                </div>

                <!-- Step 4: Documents Upload -->
                <div style="margin-bottom: 2.5rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.06);">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                        <span style="width: 28px; height: 28px; background: #6366f1; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 700;">4</span>
                        <h3 class="font-heading" style="font-size: 1.3rem;">Sənədlərin Yüklənməsi (PDF, JPG, PNG)</h3>
                    </div>

                    <div style="background: rgba(0,0,0,0.2); border: 2px dashed rgba(99, 102, 241, 0.3); border-radius: 0.75rem; padding: 2rem; text-align: center;">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size: 2.5rem; color: #818cf8; margin-bottom: 0.75rem;"></i>
                        <div style="font-size: 1rem; color: #fff; font-weight: 600; margin-bottom: 0.35rem;">Attestat/Diplom, Şəxsiyyət vəsiqəsi və ya Nəticə vərəqini seçin</div>
                        <div style="font-size: 0.8rem; color: #94a3b8; margin-bottom: 1rem;">Maksimum fayl həcmi: 10 MB</div>
                        <input type="file" name="documents[]" multiple class="form-input" style="max-width: 400px; margin: 0 auto;">
                    </div>
                </div>

                <button type="submit" class="btn gradient-btn" style="width: 100%; padding: 1rem; font-size: 1.1rem;">
                    <i class="fa-solid fa-check-circle"></i> Müraciəti Tamamla və Təsdiq Et
                </button>

            </form>

        </div>
    </div>

@endsection
