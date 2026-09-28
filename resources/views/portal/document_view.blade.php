@extends('layouts.app')

@section('title', $docTitle . ' - Rəsmi Elektron Sənəd')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <a href="{{ route('portal.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Portala Qayıt
        </a>
        <div>
            <button onclick="window.print()" class="btn btn-primary shadow-sm">
                <i class="bi bi-printer me-1"></i> Sənədi Çap Et / PDF Yüklə
            </button>
        </div>
    </div>

    <!-- Official Paper Layout -->
    <div class="bg-white p-5 shadow-lg rounded-3 border mx-auto" style="max-width: 850px; font-family: 'Times New Roman', Times, serif; color: #111;">
        
        <!-- Header -->
        <div class="row align-items-center mb-4 pb-3 border-bottom border-2 border-dark">
            <div class="col-3 text-center">
                <div class="border border-dark rounded-circle d-inline-flex align-items-center justify-content-center p-3" style="width: 85px; height: 85px;">
                    <i class="bi bi-bank fs-1 text-dark"></i>
                </div>
            </div>
            <div class="col-9 text-center">
                <h5 class="fw-bold mb-1 text-uppercase">AZƏRBAYCAN RESPUBLİKASI ELM VƏ TƏHSİL NAZİRLİYİ</h5>
                <h4 class="fw-bold mb-1 text-uppercase text-primary">BEYNƏLXALQ VƏ MİLLİ UNİVERSİTET SİSTEMİ</h4>
                <p class="small mb-0 text-muted">Tələbə Qeydiyyatı və Tədris İşləri üzrə Mərkəzi Departament</p>
                <small class="text-muted font-monospace">Kod: UNi-EDU-AZ / Sənəd №: {{ $docNumber }}</small>
            </div>
        </div>

        <!-- Document Metadata -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <strong>Tarix:</strong> {{ date('d.m.Y') }} il
            </div>
            <div>
                <strong>Arayış №:</strong> <span class="font-monospace fw-bold">{{ $docNumber }}</span>
            </div>
        </div>

        <!-- Document Title -->
        <div class="text-center my-4 py-2">
            <h3 class="fw-bold text-uppercase border-bottom d-inline-block pb-2 border-dark" style="letter-spacing: 1px;">
                ARAYIŞ
            </h3>
        </div>

        <!-- Body Content -->
        <div class="fs-5 lh-lg my-4 text-justify" style="text-indent: 40px;">
            Verilir tələbə <strong>{{ $user->name }}</strong> (Tələbə İD: <strong>{{ $user->student_id ?? 'N/A' }}</strong>) ona görə ki, o həqiqətən də Universitetin <strong>{{ $user->faculty?->name ?? 'Mühəndislik və Texnologiya' }}</strong> fakültəsinin <strong>{{ $user->department?->name ?? 'Kompüter Elmləri' }}</strong> ixtisası üzrə <strong>{{ strtoupper($user->degree_level ?? 'bachelor') }}</strong> akademik dərəcəsində əyani təhsil forması üzrə təhsil alır.
        </div>

        <div class="fs-5 lh-lg mb-4 text-justify" style="text-indent: 40px;">
            Tələbənin cari akademik göstəricisi (GPA): <strong>{{ number_format($user->gpa ?? 0, 2) }}</strong>, qazanılmış ECTS kreditlərinin sayı: <strong>{{ $user->earned_credits ?? 0 }} ECTS</strong> təşkil edir. Tələbə akademik borcu olmadan təhsilini davam etdirir.
        </div>

        <div class="fs-5 lh-lg mb-5 text-justify" style="text-indent: 40px;">
            Arayış <strong>{{ $docTitle }}</strong> məqsədilə tələb olunduğu yerə təqdim edilmək üçün verilir.
        </div>

        <!-- Signatures & Verification -->
        <div class="row align-items-end mt-5 pt-4">
            <div class="col-7">
                <p class="mb-1"><strong>Tədris İşləri üzrə Prorektor:</strong> Prof. Ə. Əliyev</p>
                <p class="mb-1"><strong>Fakültə Dekanı:</strong> Doc. M. Həsənov</p>
                <div class="mt-3">
                    <span class="badge bg-light text-dark border p-2">
                        <i class="bi bi-shield-check text-success me-1"></i> Rəqəmsal İmza ilə Təsdiqlənmişdir
                    </span>
                </div>
            </div>
            <div class="col-5 text-center">
                <!-- Electronic QR Code Seal -->
                <div class="p-3 border rounded bg-light d-inline-block text-center shadow-sm">
                    <div class="fw-bold small mb-1 text-primary">QR DOĞRULAMA KODU</div>
                    <div class="bg-dark text-white font-monospace p-2 rounded mb-1 small" style="letter-spacing: 2px;">
                        {{ $verificationHash }}
                    </div>
                    <small class="d-block text-muted" style="font-size: 0.75rem;">
                        Scan QR & Verify Authenticity
                    </small>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-5 pt-4 border-top text-center text-muted small">
            Bu sənəd Azərbaycan Respublikasının "Elektron imza və elektron sənəd haqqında" Qanununa əsasən elektron şəkildə hazırlanmışdır və rəsmi hüquqi qüvvəyə malikdir. Sənədin həqiqiliyini <code>{{ $verificationUrl }}</code> ünvanından yoxlaya bilərsiniz.
        </div>
    </div>
</div>
@endsection
