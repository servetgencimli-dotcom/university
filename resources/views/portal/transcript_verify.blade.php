@extends('layouts.app')

@section('title', 'Rəsmi Transkript və Sənəd Doğrulama Portalı')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-8">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header p-4 text-white text-center {{ $isValid ? 'bg-gradient-success' : 'bg-gradient-danger' }}" style="background: {{ $isValid ? 'linear-gradient(135deg, #0d6efd 0%, #052c65 100%)' : 'linear-gradient(135deg, #dc3545 0%, #842029 100%)' }};">
                    <div class="my-2">
                        @if($isValid)
                            <div class="d-inline-flex align-items-center justify-content-center bg-white text-primary rounded-circle mb-3 shadow" style="width: 70px; height: 70px; font-size: 2.2rem;">
                                <i class="bi bi-shield-check text-success"></i>
                            </div>
                            <h3 class="fw-bold mb-1">RƏSMİ SƏNƏD TƏSDİQLƏNDİ</h3>
                            <p class="mb-0 opacity-75">Azərbaycan Respublikası Elm və Təhsil Nazirliyi / Universitet Portalı</p>
                        @else
                            <div class="d-inline-flex align-items-center justify-content-center bg-white text-danger rounded-circle mb-3 shadow" style="width: 70px; height: 70px; font-size: 2.2rem;">
                                <i class="bi bi-shield-x text-danger"></i>
                            </div>
                            <h3 class="fw-bold mb-1">SƏNƏD TAPILMADI VƏ YA ETİBARSIZDIR</h3>
                            <p class="mb-0 opacity-75">Doğrulama Kodu: <code>{{ $code }}</code></p>
                        @endif
                    </div>
                </div>

                <div class="card-body p-4 p-md-5 bg-white">
                    @if($isValid)
                        <div class="alert alert-success d-flex align-items-center rounded-3 mb-4 border-0 bg-success-subtle text-success-emphasis">
                            <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                            <div>
                                <strong>Rəsmi E-Doğrulama Tamamlandı:</strong> Bu təhsil transkripti və akademik göstəricilər universitetin Mərkəzi Reystr Bazasında eyniləşdirilmişdir.
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-sm-6">
                                <div class="p-3 bg-light rounded-3 h-100 border">
                                    <span class="text-muted small d-block mb-1">Tələbənin Tam Adı</span>
                                    <h5 class="fw-bold mb-0 text-dark">{{ $student->name }}</h5>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 bg-light rounded-3 h-100 border">
                                    <span class="text-muted small d-block mb-1">Tələbə İD (Şəxsi Hesab №)</span>
                                    <h5 class="fw-bold mb-0 text-dark">{{ $student->student_id ?? 'N/A' }}</h5>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="p-3 bg-light rounded-3 h-100 border">
                                    <span class="text-muted small d-block mb-1">Fakültə</span>
                                    <span class="fw-semibold text-dark">{{ $student->faculty?->name ?? 'Mühəndislik və Yüksək Texnologiyalar' }}</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 bg-light rounded-3 h-100 border">
                                    <span class="text-muted small d-block mb-1">İxtisas / Kafedra</span>
                                    <span class="fw-semibold text-dark">{{ $student->department?->name ?? 'Kompüter Elmləri və Proqramlaşdırma' }}</span>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="p-3 bg-primary-subtle text-primary-emphasis rounded-3 h-100 border border-primary-subtle text-center">
                                    <span class="small d-block mb-1">Akademik Dərəcə</span>
                                    <h5 class="fw-bold mb-0">{{ strtoupper($student->degree_level ?? 'bachelor') }}</h5>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="p-3 bg-info-subtle text-info-emphasis rounded-3 h-100 border border-info-subtle text-center">
                                    <span class="small d-block mb-1">Ümumi GPA (ÜOMG)</span>
                                    <h4 class="fw-bold mb-0">{{ number_format($cGPA, 2) }} / 4.00</h4>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="p-3 bg-success-subtle text-success-emphasis rounded-3 h-100 border border-success-subtle text-center">
                                    <span class="small d-block mb-1">Qazanılmış ECTS Kredit</span>
                                    <h4 class="fw-bold mb-0">{{ $earnedCredits }} Kredit</h4>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 bg-dark text-white rounded-3 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <small class="text-white-50 d-block">Elektron Rəqəmsal İmza & Hash Kodu:</small>
                                <code class="text-warning font-monospace fs-6">{{ $verificationHash }}</code>
                            </div>
                            <div class="text-end">
                                <small class="text-white-50 d-block">Yoxlanılma Tarixi:</small>
                                <span class="badge bg-secondary font-monospace">{{ $verifiedAt }}</span>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button onclick="window.print()" class="btn btn-outline-dark me-2">
                                <i class="bi bi-printer me-1"></i> Sənədi Çap Et
                            </button>
                            <a href="{{ route('home') }}" class="btn btn-primary">
                                <i class="bi bi-house me-1"></i> Əsas Səhifəyə Qayıt
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="lead text-muted mb-4">{{ $message }}</p>
                            <div class="p-3 bg-light rounded-3 border d-inline-block text-start mb-4">
                                <span class="small text-muted d-block mb-1">Ehtimal olunan səbəblər:</span>
                                <ul class="mb-0 text-secondary ps-3 small">
                                    <li>Daxil edilmiş doğrulama kodu səhvdir və ya köhnəlmişdir.</li>
                                    <li>Sənəd ləğv edilib və ya sistemdə qeydə alınmayıb.</li>
                                    <li>QR kodun oxunması zamanı simvol xətası baş verib.</li>
                                </ul>
                            </div>
                            <div>
                                <a href="{{ route('home') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-1"></i> Əsas Səhifəyə Qayıt
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-light p-3 text-center text-muted small">
                    Universitet Rəqəmsal Sənəd Doğrulama Sistemi &copy; {{ date('Y') }} | Qanuni Gücə Malikdir
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
