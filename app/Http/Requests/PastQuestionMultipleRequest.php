<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PastQuestionMultipleRequest extends FormRequest
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
            'past_questions' => 'required|array|max:25',
            'past_questions.*' => 'required_unless:past_questions,'.null.'|uuid|max:100',
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
            'past_questions.required' => 'A past question is required',
            'past_questions.array' => 'Past questions selection must be in the required type',
            'past_questions.max' => 'Past questions selection can not have more than 25 items',
            'past_questions.*' => 'One of the past question selection item is either not valid or has more than 100 characters',
        ];
    }
}
