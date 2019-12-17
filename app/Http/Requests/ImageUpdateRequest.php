<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImageUpdateRequest extends FormRequest
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
            'photos' => 'required|array|max:1|filled',
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
            'id.required' => 'An id is required',
            'id.uuid' => 'ID characters are not valid',
            'id.max' => 'ID can not have more than 100 characters',

            'photos.required' => 'A photo is required',
            'photos.array' => 'The photos field is not valid',
            'photos.max' => 'Can not have more than 1 photo',
            'photos.filled' => 'The photos field is actually empty',
            'photos.*' => 'Only image files are allowed in photos field',
        ];
    }
}
