<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentStoreRequest extends FormRequest
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
            'past_question_id' => 'required|uuid|max:100',
            'comment' => 'required_if:reply,'.null.'|string|max:250',
            'reply' => 'required_if:comment,'.null.'|string|max:250',
            'parent_comment_id' => 'required_unless:reply,'.null.'|uuid|max:100',
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
            'past_question_id.required' => 'A past question id is required',
            'past_question_id.uuid' => 'Past question id characters are not valid',
            'past_question_id.max' => 'Past question id can not have more than 100 characters',

            'comment.required' => 'A comment is required',
            'comment.string' => 'The comment field is not valid',
            'comment.max' => 'Can not have more than 250 characters',

            'reply.required' => 'A reply is required',
            'reply.string' => 'The reply field is not valid',
            'reply.max' => 'Can not have more than 250 characters',

            'parent_comment_id.required' => 'A parent comment id is required',
            'parent_comment_id.uuid' => 'Parent comment id characters are not valid',
            'parent_comment_id.max' => 'Parent comment id can not have more than 100 characters',
        ];
    }
}
