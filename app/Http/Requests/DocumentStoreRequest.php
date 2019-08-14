<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentStoreRequest extends FormRequest
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
            'docs' => 'required|array|max:10|filled',
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
            'past_question_id.required' => 'A past question id is required',
            'past_question_id.uuid' => 'Past question id characters are not valid',
            'past_question_id.max' => 'Past question id can not have more than 100 characters',

            'docs.required' => 'A documents is required',
            'docs.array' => 'The documents field is not valid',
            'docs.max' => 'Can not have more than 10 documents',
            'docs.filled' => 'The documents field is actually empty',
            'docs.*' => 'Only document files are allowed in documents field',
        ];
    }
}
