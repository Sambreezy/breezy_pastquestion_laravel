<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentMultipleRequest extends FormRequest
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
            'documents' => 'required|array|max:25',
            'documents.*' => 'required_unless:documents,'.null.'|uuid|max:100',
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
            'documents.required' => 'A document is required',
            'documents.array' => 'Documents selection must be in the required type',
            'documents.max' => 'Documents selection can not have more than 25 items',
            'documents.*' => 'One of the document selection item is either not valid or has more than 100 characters',
        ];
    }
}
