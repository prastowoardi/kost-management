<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ParsesRupiah;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
{
    use ParsesRupiah;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'amount' => $this->parseRupiah($this->input('amount')),
            'late_fee' => $this->parseRupiah($this->input('late_fee') ?? 0),
        ]);
    }

    public function rules(): array
    {
        return [
            'tenant_id' => 'required|exists:tenants,id',
            'payment_date' => 'required|date',
            'period_month' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'late_fee' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,paid,overdue',
            'payment_method' => 'nullable|in:cash,transfer,e-wallet',
            'notes' => 'nullable|string',
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }
}
