<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImageStoreRequest extends FormRequest
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
            'photos' => 'required|array|max:10|filled',
            'photos.*' => 'required_unless:photos,'.null.'|image',
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

            'photos.required' => 'A photo is required',
            'photos.array' => 'The photos field is not valid',
            'photos.max' => 'Can not have more than 10 photos',
            'photos.filled' => 'The photos field is actually empty',
            'photos.*' => 'Only image files are allowed in photos field',
        ];
    }
}
