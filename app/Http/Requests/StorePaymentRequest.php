<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $amountInput = $this->input('amount');
        $lateFeeInput = $this->input('late_fee');

        $amountClean = preg_replace('/[^0-9]/', '', $amountInput);
        $lateFeeClean = preg_replace('/[^0-9]/', '', $lateFeeInput);

        dd([
            'amount_sent' => $amountInput,
            'amount_cleaned' => $amountClean,
        ]);

        $this->merge([
            'amount' => $amountClean,
            'late_fee' => $lateFeeClean,
        ]);
    }

    public function rules()
    {
        return [
            'amount' => 'required|integer|min:1', 
            'late_fee' => 'nullable|integer|min:0',
        ];
    }
}