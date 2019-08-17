<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SocialVoteRequest extends FormRequest
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
            'past_question_id.max' => 'A past question id can not have more than 100 characters',
        ];
    }
}
