<?php

namespace RolfHaug\FrontSms\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInboundSms extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required',
            'to' => 'required',
            'from' => 'required',
            'text' => 'required',
            'sent' => 'required',
            'counter' => 'required',
            'keyword' => 'required',
            'files' => 'sometimes|array',
        ];
    }
}