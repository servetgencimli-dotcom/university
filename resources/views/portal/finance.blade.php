@extends('layouts.portal')

@section('title', 'Təhsil Haqqı və Maliyyə Portalı — Khazar University')
@section('header_title', 'Təhsil Haqqı Fakturaları & Onlayn Ödəniş Sistemi')

@section('content')

    <!-- Top KPI Cards -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; margin-bottom: 2.5rem;">
        
        <div class="glass-card" style="padding: 1.25rem; border-left: 4px solid {{ $totalDebt > 0 ? '#ef4444' : '#10b981' }};">
            <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; margin-bottom: 0.4rem;">CARİ BORC STATUSU</div>
            <div class="font-heading" style="font-size: 1.8rem; color: {{ $totalDebt > 0 ? '#f87171' : '#34d399' }};">
                {{ number_format($totalDebt, 2) }} ₼
            </div>
            <div style="font-size: 0.75rem; color: #cbd5e1; margin-top: 0.25rem;">
                @if($totalDebt > 0)
                    <span style="color: #fca5a5;"><i class="fa-solid fa-circle-exclamation"></i> Ödənilməsi tələb olunan faktura var</span>
                @else
                    <span style="color: #34d399;"><i class="fa-solid fa-circle-check"></i> Borcunuz yoxdur</span>
                @endif
            </div>
        </div>

        <div class="glass-card" style="padding: 1.25rem; border-left: 4px solid #38bdf8;">
            <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; margin-bottom: 0.4rem;">CƏMİ ÖDƏNİLMİŞ MƏBLƏĞ</div>
            <div class="font-heading" style="font-size: 1.8rem; color: #fff;">
                {{ number_format($totalPaid, 2) }} ₼
            </div>
            <div style="font-size: 0.75rem; color: #38bdf8; margin-top: 0.25rem;">
                Universitet hesabına köçürülüb
            </div>
        </div>

        <div class="glass-card" style="padding: 1.25rem; border-left: 4px solid #a855f7;">
            <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; margin-bottom: 0.4rem;">TƏQAÜD STATUSU (GPA Kalkulyatoru)</div>
            <div class="font-heading" style="font-size: 1.4rem; color: #e9d5ff;">
                {{ $scholarship['amount'] > 0 ? number_format($scholarship['amount'], 2) . ' ₼/Ay' : '0.00 ₼' }}
            </div>
            <div style="font-size: 0.75rem; color: #d8b4fe; margin-top: 0.25rem;" title="{{ $scholarship['title'] }}">
                <i class="fa-solid fa-award me-1"></i> {{ Str::limit($scholarship['title'], 32) }}
            </div>
        </div>

        <div class="glass-card" style="padding: 1.25rem; border-left: 4px solid #818cf8;">
            <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; margin-bottom: 0.4rem;">ELEKTRON ARAYIŞ (E-ARAYIŞ)</div>
            <div style="margin-top: 0.35rem;">
                <a href="{{ route('portal.certificate.generate', ['type' => 'bank']) }}" target="_blank" class="btn btn-outline" style="font-size: 0.8rem; padding: 0.35rem 0.75rem; width: 100%; border-color: rgba(129, 140, 248, 0.4); color: #a5b4fc;">
                    <i class="fa-solid fa-file-pdf"></i> Bank / Maliyyə Arayışı Al
                </a>
            </div>
        </div>

    </div>

    <!-- Invoices List -->
    <div class="glass-panel" style="padding: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div>
                <h3 class="font-heading" style="font-size: 1.35rem; color: #fff;">
                    <i class="fa-solid fa-receipt text-emerald-400"></i> Bütün Təhsil Haqqı Fakturaları
                </h3>
                <p style="font-size: 0.85rem; color: #94a3b8;">Fakturalarınızı birbaşa onlayn bank kartı ilə ödəyə və rəsmi qəbzləri yükləyə bilərsiniz.</p>
            </div>
            <span class="badge badge-primary">{{ $invoices->count() }} Faktura</span>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.1); color: #94a3b8; font-size: 0.8rem; text-transform: uppercase;">
                        <th style="padding: 0.75rem 1rem;">FAKTURA №</th>
                        <th style="padding: 0.75rem 1rem;">SEMSETR</th>
                        <th style="padding: 0.75rem 1rem;">MƏBLƏĞ</th>
                        <th style="padding: 0.75rem 1rem;">SON ÖDƏNİŞ TARİXİ</th>
                        <th style="padding: 0.75rem 1rem;">STATUS</th>
                        <th style="padding: 0.75rem 1rem; text-align: right;">ƏMƏLİYYAT / QƏBZ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                            <td class="font-mono" style="padding: 1rem; color: #818cf8; font-weight: 700;">
                                {{ $inv->invoice_number }}
                                @if($inv->receipt_number)
                                    <div style="font-size: 0.75rem; color: #34d399;">Qəbz: {{ $inv->receipt_number }}</div>
                                @endif
                            </td>

                            <td style="padding: 1rem; font-weight: 600; color: #fff;">
                                {{ $inv->semester_term }}
                            </td>

                            <td style="padding: 1rem; font-weight: 700; color: #38bdf8; font-size: 1.1rem;">
                                {{ number_format($inv->amount, 2) }} ₼
                            </td>

                            <td style="padding: 1rem; font-size: 0.85rem; color: #cbd5e1;">
                                {{ $inv->due_date?->format('d.m.Y') ?? 'Təyin edilməyib' }}
                                @if($inv->status === 'pending' && $inv->due_date && $inv->due_date < now())
                                    <span class="badge badge-danger" style="font-size: 0.7rem; margin-left: 0.3rem;">Gecikib</span>
                                @endif
                            </td>

                            <td style="padding: 1rem;">
                                @if($inv->status === 'paid')
                                    <span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> Ödənilib</span>
                                    <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.2rem;">{{ $inv->paid_at?->format('d.m.Y H:i') }}</div>
                                @else
                                    <span class="badge badge-warning"><i class="fa-solid fa-clock"></i> Ödəniş Gözləyir</span>
                                @endif
                            </td>

                            <td style="padding: 1rem; text-align: right;">
                                @if($inv->status === 'paid')
                                    <a href="{{ route('portal.finance.receipt', $inv->id) }}" class="btn btn-outline" style="padding: 0.4rem 0.85rem; font-size: 0.8rem; color: #34d399; border-color: rgba(52, 211, 153, 0.4);">
                                        <i class="fa-solid fa-receipt"></i> Rəsmi Qəbz
                                    </a>
                                @else
                                    <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                        <button onclick="openPaymentModal({{ $inv->id }}, '{{ $inv->invoice_number }}', '{{ $inv->semester_term }}', {{ $inv->amount }})" class="btn gradient-btn" style="padding: 0.45rem 0.85rem; font-size: 0.85rem;">
                                            <i class="fa-solid fa-credit-card"></i> İndi Ödə
                                        </button>
                                        <button onclick="openInstallmentModal({{ $inv->id }}, '{{ $inv->invoice_number }}', {{ $inv->amount }})" class="btn btn-outline" style="padding: 0.45rem 0.75rem; font-size: 0.8rem; color: #a855f7; border-color: rgba(168, 85, 247, 0.4);">
                                            <i class="fa-solid fa-calculator"></i> Taksitə Böl
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #94a3b8; padding: 2rem;">Faktura tapılmadı.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL: Online Bank Card Payment Checkout Gateway -->
    <div id="modal-payment" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
        <div class="glass-panel" style="background: #091322; border: 1px solid rgba(0, 163, 224, 0.4); border-radius: 1rem; width: 100%; max-width: 500px; padding: 2.25rem;">
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #002B49, #00A3E0); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; color: #fff;">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div>
                        <h3 class="font-heading" style="font-size: 1.25rem; color: #fff;">Təhlükəsiz Onlayn Ödəniş</h3>
                        <div style="font-size: 0.75rem; color: #38bdf8;">Khazar University E-Payment Gateway</div>
                    </div>
                </div>
                <button onclick="document.getElementById('modal-payment').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.2rem; cursor: pointer;">&times;</button>
            </div>

            <!-- Invoice Details Card -->
            <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 0.6rem; padding: 1rem; margin-bottom: 1.5rem;">
                <div style="display: flex; justify-content: space-between; font-size: 0.85rem; color: #94a3b8; margin-bottom: 0.35rem;">
                    <span>Faktura Nömrəsi:</span>
                    <strong id="pay-inv-number" style="color: #fff; font-family: monospace;"></strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.85rem; color: #94a3b8; margin-bottom: 0.35rem;">
                    <span>Semestr:</span>
                    <strong id="pay-inv-term" style="color: #fff;"></strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 1.1rem; border-top: 1px solid rgba(255,255,255,0.06); padding-top: 0.5rem; margin-top: 0.5rem;">
                    <span style="font-weight: 700; color: #fff;">Ödəniləcək Məbləğ:</span>
                    <strong id="pay-inv-amount" style="color: #34d399; font-size: 1.3rem;"></strong>
                </div>
            </div>

            <!-- Payment Form -->
            <form id="form-pay-online" method="POST" action="">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label style="font-size: 0.8rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Bank Kartının Nömrəsi</label>
                    <input type="text" name="card_number" required placeholder="4169 7388 0000 0000" maxlength="19" class="form-input" style="font-family: monospace; font-size: 1rem; letter-spacing: 0.05em;">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="font-size: 0.8rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Kart Sahibinin Adı Soyadı</label>
                    <input type="text" name="card_holder" required placeholder="MURAD HUSEYNOV" class="form-input" style="text-transform: uppercase;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="font-size: 0.8rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">Bitmə Tarixi (AA/İİ)</label>
                        <input type="text" name="expiry" required placeholder="12/28" maxlength="5" class="form-input" style="font-family: monospace;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">CVV / CVC (3 rəqəm)</label>
                        <input type="password" name="cvv" required placeholder="•••" maxlength="4" class="form-input" style="font-family: monospace;">
                    </div>
                </div>

                <button type="submit" class="btn gradient-btn" style="width: 100%; padding: 0.85rem; font-size: 1rem;">
                    <i class="fa-solid fa-lock"></i> Ödənişi Təsdiqlə və İcra Et
                </button>
            </form>

        </div>
    </div>

    <!-- MODAL: Installment Payment Calculator & Division Plan -->
    <div id="modal-installment" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
        <div class="glass-panel" style="background: #091322; border: 1px solid rgba(168, 85, 247, 0.4); border-radius: 1rem; width: 100%; max-width: 480px; padding: 2.25rem;">
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #581c87, #a855f7); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; color: #fff;">
                        <i class="fa-solid fa-calculator"></i>
                    </div>
                    <div>
                        <h3 class="font-heading" style="font-size: 1.25rem; color: #fff;">Təhsil Haqqı Bölünməsi</h3>
                        <div style="font-size: 0.75rem; color: #c084fc;">Mərhələli Taksit Planı Yaradılması</div>
                    </div>
                </div>
                <button onclick="document.getElementById('modal-installment').style.display='none'" style="background: none; border: none; color: #94a3b8; font-size: 1.2rem; cursor: pointer;">&times;</button>
            </div>

            <form id="form-installment" method="POST" action="">
                @csrf
                <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 0.6rem; padding: 1rem; margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.85rem; color: #94a3b8; margin-bottom: 0.35rem;">
                        <span>Ana Faktura №:</span>
                        <strong id="inst-inv-number" style="color: #fff; font-family: monospace;"></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 1.1rem; border-top: 1px solid rgba(255,255,255,0.06); padding-top: 0.5rem; margin-top: 0.5rem;">
                        <span style="font-weight: 700; color: #fff;">Bölünəcək Məbləğ:</span>
                        <strong id="inst-inv-amount" style="color: #c084fc; font-size: 1.2rem;"></strong>
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="font-size: 0.85rem; color: #cbd5e1; display: block; margin-bottom: 0.5rem;">Taksit Hissələrinin Sayı:</label>
                    <select name="parts" class="form-input" style="background: rgba(15, 23, 42, 0.9); font-size: 1rem; color: #fff; padding: 0.65rem;">
                        <option value="2">2 Bərabər Hissə (50% + 50%)</option>
                        <option value="3">3 Bərabər Hissə (33% + 33% + 34%)</option>
                        <option value="4">4 Bərabər Hissə (25% + 25% + 25% + 25%)</option>
                    </select>
                </div>

                <button type="submit" class="btn gradient-btn" style="width: 100%; padding: 0.85rem; font-size: 1rem; background: linear-gradient(135deg, #7e22ce, #a855f7);">
                    <i class="fa-solid fa-check-double"></i> Qrafiki Təsdiqlə və Fakturaları Yenilə
                </button>
            </form>

        </div>
    </div>

@endsection

@section('scripts')
<script>
    function openPaymentModal(invoiceId, invNumber, term, amount) {
        document.getElementById('pay-inv-number').innerText = invNumber;
        document.getElementById('pay-inv-term').innerText = term;
        document.getElementById('pay-inv-amount').innerText = Number(amount).toFixed(2) + ' ₼';
        document.getElementById('form-pay-online').action = `/portal/finance/invoices/${invoiceId}/pay`;
        document.getElementById('modal-payment').style.display = 'flex';
    }

    function openInstallmentModal(invoiceId, invNumber, amount) {
        document.getElementById('inst-inv-number').innerText = invNumber;
        document.getElementById('inst-inv-amount').innerText = Number(amount).toFixed(2) + ' ₼';
        document.getElementById('form-installment').action = `/portal/finance/invoices/${invoiceId}/installments`;
        document.getElementById('modal-installment').style.display = 'flex';
    }
</script>
@endsection
