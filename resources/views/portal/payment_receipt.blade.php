@extends('layouts.portal')

@section('title', "Ödəniş Qəbzi #{$invoice->receipt_number} — Khazar University")
@section('header_title', 'Rəsmi Elektron Ödəniş Qəbzi')

@section('content')

    <!-- Top Action Bar (Hidden on Print) -->
    <div class="no-print" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <a href="{{ route('portal.finance.index') }}" class="btn btn-outline" style="padding: 0.4rem 0.85rem; font-size: 0.85rem; margin-bottom: 0.5rem; display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-arrow-left"></i> Maliyyə Panelinə Qayıt
            </a>
            <h2 class="font-heading" style="font-size: 1.6rem; color: #fff;">Ödəniş Qəbzi #{{ $invoice->receipt_number }}</h2>
        </div>

        <button onclick="window.print()" class="btn gradient-btn" style="padding: 0.65rem 1.25rem;">
            <i class="fa-solid fa-print"></i> Qəbzi Çap Et / PDF
        </button>
    </div>

    <!-- Official Printable Payment Receipt Paper -->
    <div class="receipt-paper" style="background: #ffffff; color: #0f172a; max-width: 680px; margin: 0 auto; padding: 3rem; border-radius: 0.75rem; box-shadow: 0 10px 30px rgba(0,0,0,0.5); font-family: 'Segoe UI', Arial, sans-serif;">
        
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #002B49; padding-bottom: 1.5rem; margin-bottom: 2rem;">
            <div>
                <div style="font-size: 1.4rem; font-weight: 800; color: #002B49; letter-spacing: -0.01em;">XƏZƏR UNİVERSİTETİ</div>
                <div style="font-size: 0.95rem; font-weight: 600; color: #64748b;">KHAZAR UNIVERSITY</div>
                <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.25rem;">Maliyyə və Mühasibatlıq Departamenti</div>
            </div>

            <div style="text-align: right;">
                <div style="display: inline-block; background: #ecfdf5; border: 1.5px solid #10b981; color: #065f46; font-weight: 700; padding: 0.35rem 0.75rem; border-radius: 0.4rem; font-size: 0.85rem;">
                    <i class="fa-solid fa-circle-check"></i> ÖDƏNİLİB (PAID)
                </div>
                <div class="font-mono" style="font-size: 0.8rem; color: #64748b; margin-top: 0.35rem;">Qəbz №: <strong>{{ $invoice->receipt_number }}</strong></div>
            </div>
        </div>

        <!-- Meta Grid -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; font-size: 0.85rem; margin-bottom: 2rem; background: #f8fafc; padding: 1.25rem; border-radius: 0.5rem; border: 1px solid #e2e8f0;">
            <div>
                <div style="color: #64748b; font-size: 0.75rem;">TƏLƏBƏ:</div>
                <div style="font-weight: 700; font-size: 1rem; color: #0f172a;">{{ $invoice->student->name }}</div>
                <div style="font-family: monospace; color: #3b82f6;">ID: {{ $invoice->student->student_id ?? 'KU-STU' }}</div>
                <div style="color: #64748b; margin-top: 0.25rem;">{{ $invoice->student->faculty->name ?? 'Xəzər Universiteti' }}</div>
            </div>

            <div>
                <div style="color: #64748b; font-size: 0.75rem;">ÖDƏNİŞ DETALLARI:</div>
                <div>Faktura №: <strong>{{ $invoice->invoice_number }}</strong></div>
                <div>Semestr: <strong>{{ $invoice->semester_term }}</strong></div>
                <div>Tarix: <strong>{{ $invoice->paid_at?->format('d.m.Y H:i') ?? now()->format('d.m.Y') }}</strong></div>
                <div style="font-family: monospace; font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">Tranzaksiya: {{ $invoice->transaction_id }}</div>
            </div>
        </div>

        <!-- Receipt Table -->
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem; margin-bottom: 2rem;">
            <thead>
                <tr style="background: #f1f5f9; border-bottom: 1px solid #cbd5e1; color: #334155;">
                    <th style="padding: 0.75rem; border: 1px solid #e2e8f0;">Təyinat / Description</th>
                    <th style="padding: 0.75rem; border: 1px solid #e2e8f0; text-align: right;">Məbləğ / Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 0.75rem; border: 1px solid #e2e8f0;">
                        Təhsil Haqqı Ödənişi ({{ $invoice->semester_term }})
                        <div style="font-size: 0.75rem; color: #64748b;">Ödəniş üsulu: {{ $invoice->payment_method }}</div>
                    </td>
                    <td style="padding: 0.75rem; border: 1px solid #e2e8f0; text-align: right; font-weight: 700;">
                        {{ number_format($invoice->amount, 2) }} ₼
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr style="background: #f8fafc; font-weight: 800; font-size: 1.1rem; color: #002B49;">
                    <td style="padding: 0.75rem; border: 1px solid #e2e8f0;">YEKUN ÖDƏNİLƏN:</td>
                    <td style="padding: 0.75rem; border: 1px solid #e2e8f0; text-align: right; color: #059669;">
                        {{ number_format($invoice->amount, 2) }} AZN
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- Security & QR Footer -->
        <div style="border-top: 1px solid #e2e8f0; padding-top: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <div style="font-size: 0.75rem; color: #64748b; line-height: 1.5;">
                <div>Bu sənəd elektron fiskal təsdiq daşıyır və rəsmi ödəniş qəbzi sayılır.</div>
                <div>Xəzər Universiteti Rəsmi E-Ödəniş Gateway Sistemi</div>
            </div>

            <div style="border: 1px solid #cbd5e1; padding: 0.35rem; border-radius: 0.4rem; text-align: center;">
                <i class="fa-solid fa-qrcode" style="font-size: 2.5rem; color: #002B49;"></i>
                <div style="font-size: 0.6rem; color: #64748b; font-family: monospace;">TƏSDİQ EDİLDİ</div>
            </div>
        </div>

    </div>

@endsection

@section('scripts')
<style>
    @media print {
        body { background: #fff !important; }
        .portal-sidebar, .no-print, header { display: none !important; }
        .portal-content { margin-left: 0 !important; padding: 0 !important; width: 100% !important; }
        .receipt-paper { box-shadow: none !important; border: 1px solid #ccc !important; }
    }
</style>
@endsection
