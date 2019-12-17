<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SocialFlagRequest extends FormRequest
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
            'comment_id' => 'required|uuid|max:100',
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
            'comment_id.required' => 'A comment id is required',
            'comment_id.uuid' => 'Comment id characters are not valid',
            'comment_id.max' => 'A comment id can not have more than 100 characters',
        ];
    }
}
