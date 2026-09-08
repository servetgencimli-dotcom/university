<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\SystemNotification;
use App\Models\TuitionInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FinanceController extends Controller
{
    /**
     * Show student's financial invoices and payment portal.
     */
    public function index()
    {
        $user = Auth::user();
        $invoices = TuitionInvoice::where('student_id', $user->id)->latest()->get();

        $totalDebt = $invoices->where('status', 'pending')->sum('amount');
        $totalPaid = $invoices->where('status', 'paid')->sum('amount');

        return view('portal.finance', compact('user', 'invoices', 'totalDebt', 'totalPaid'));
    }

    /**
     * Process simulated online card payment for tuition invoice.
     */
    public function payOnline(Request $request, $invoiceId)
    {
        $user = Auth::user();
        $invoice = TuitionInvoice::findOrFail($invoiceId);

        if ($invoice->student_id !== $user->id && ! $user->isAdmin()) {
            abort(403);
        }

        if ($invoice->status === 'paid') {
            return back()->with('error', 'Bu faktura artıq ödənilib.');
        }

        $validated = $request->validate([
            'card_number' => 'required|string|min:16|max:19',
            'card_holder' => 'required|string|max:100',
            'expiry' => 'required|string|max:7',
            'cvv' => 'required|string|min:3|max:4',
        ]);

        $txnId = 'TXN-KU-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        $receiptNum = 'KU-REC-2026-' . rand(10000, 99999);

        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => 'Onlayn Bank Kartı (Visa/Mastercard)',
            'transaction_id' => $txnId,
            'receipt_number' => $receiptNum,
            'paid_amount' => $invoice->amount,
            'gateway_response' => [
                'status' => 'SUCCESS',
                'card_mask' => '**** **** **** ' . substr(str_replace(' ', '', $validated['card_number']), -4),
                'card_holder' => strtoupper($validated['card_holder']),
                'processed_at' => now()->toIso8601String(),
            ],
        ]);

        ActivityLog::log(
            'PAYMENT_COMPLETED',
            "Tələbə {$user->name} {$invoice->amount} ₼ məbləğində təhsil haqqı fakturasını (#{$invoice->invoice_number}) onlayn ödədi.",
            $invoice
        );

        SystemNotification::send(
            $user,
            'Ödəniş Qəbul Edildi',
            "Faktura #{$invoice->invoice_number} üzrə {$invoice->amount} ₼ ödənişiniz uğurla tamamlandı. Qəbz №: {$receiptNum}",
            'finance',
            route('portal.finance.receipt', $invoice->id)
        );

        return redirect()->route('portal.finance.receipt', $invoice->id)
            ->with('success', "Ödəniş uğurla tamamlandı! Qəbz №: {$receiptNum}");
    }

    /**
     * Show official payment receipt.
     */
    public function receipt($invoiceId)
    {
        $user = Auth::user();
        $invoice = TuitionInvoice::with('student')->findOrFail($invoiceId);

        if ($invoice->student_id !== $user->id && ! $user->isAdmin()) {
            abort(403);
        }

        return view('portal.payment_receipt', compact('invoice'));
    }
}
