<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentMultipleRequest extends FormRequest
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
            'comments' => 'required|array|max:25',
            'comments.*' => 'required_unless:comments,'.null.'|uuid|max:100',
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
            'comments.required' => 'A comment is required',
            'comments.array' => 'Comment selection must be in the required type',
            'comments.max' => 'Comment selection can not have more than 25 items',
            'comments.*' => 'One of the comment selection item is either not valid or has more than 100 characters',
        ];
    }
}
