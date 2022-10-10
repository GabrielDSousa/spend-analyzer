<?php


namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class StoreMapRequest extends FormRequest
{
    protected const REQUIRED_STRING = 'required|string';

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->tokenCan('map:create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'date' => self::REQUIRED_STRING,
            'date_format' => self::REQUIRED_STRING,
            'bank' => self::REQUIRED_STRING,
            'amount' => self::REQUIRED_STRING,
            'description' => self::REQUIRED_STRING,
            'type' => self::REQUIRED_STRING
        ];
    }
}
