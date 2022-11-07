<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->tokenCan('transaction:update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $str = 'string|nullable';
        return [
            'date' => 'date_format:Y-m-d',
            'amount' => 'numeric',
            'description' => $str,
            'file' => $str,
            'type' => $str,
            'bank' => $str
        ];
    }
}
