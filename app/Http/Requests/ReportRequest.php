<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
{
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
     * @return array
     */
    public function rules()
    {
        return [
            'field:comp-l8imfdku' => 'required',
            'field:comp-l8imhl32' => 'required',
            'field:comp-l8ijfnkh' => 'required',
            'field:comp-l8ikjksx' => 'required',
            'theme' => 'optional'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.required' => 'Please fill the title.',
        ];
    }

}