<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentUpdateRequest extends FormRequest
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
            'comment' => 'required_if:reply,'.null.'|string|max:250',
            'reply' => 'required_if:comment,'.null.'|string|max:250',
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

            'comment.required' => 'A comment is required',
            'comment.string' => 'The comment field is not valid',
            'comment.max' => 'Can not have more than 250 characters',

            'reply.required' => 'A reply is required',
            'reply.string' => 'The reply field is not valid',
            'reply.max' => 'Can not have more than 250 characters',
        ];
    }
}
