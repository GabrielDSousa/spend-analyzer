<?php


namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class LoadCsvRequest extends FormRequest
{
    private const REQUIRED_STRING = 'required|string';

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
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
            'link' => self::REQUIRED_STRING,
            'filename' => self::REQUIRED_STRING,
            'bank' => self::REQUIRED_STRING,
            'type' => self::REQUIRED_STRING,
        ];
    }
}
