<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentUpdateRequest extends FormRequest
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
            'id' => 'required|uuid|max:100',
            'docs' => 'required|array|max:1|filled',
            'docs.*' => 'required_unless:docs,'.null.'|file',
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
            'id.required' => 'An id is required',
            'id.uuid' => 'ID characters are not valid',
            'id.max' => 'ID can not have more than 100 characters',

            'docs.required' => 'A photo is required',
            'docs.array' => 'The documents field is not valid',
            'docs.max' => 'Can not have more than 1 photo',
            'docs.filled' => 'The documents field is actually empty',
            'docs.*' => 'Only document files are allowed in documents field',
        ];
    }
}
