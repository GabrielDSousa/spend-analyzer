<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //TODO: login logic will be here
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'date' => 'required|date_format:Y-m-d',
            'amount' => 'required|numeric',
            'description' => 'required|string',
            'file' => 'string|nullable',
            'type' => 'required|string',
            'bank' => 'string|nullable'
        ];
    }
}
