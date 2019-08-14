<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            'name' => 'sometimes|required|string|max:25',
            'phone' => 'sometimes|required|string|max:25',
            'photos' => 'sometimes|required|array|max:1|filled',
            'photos.*' => 'required_unless:photos,'.null.'|image',
            'description' => 'sometimes|required|string|max:250',
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

            'name.required' => 'A name is required',
            'name.string' => 'The name field is not valid',
            'name.max' => 'Can not have more than 250 characters',

            'phone.required' => 'A phone is required',
            'phone.string' => 'The phone field is not valid',
            'phone.max' => 'Can not have more than 25 characters',

            'photos.required' => 'A photo is required',
            'photos.array' => 'The photos field is not valid',
            'photos.max' => 'Can not have more than 1 photo',
            'photos.filled' => 'The photos field is actually empty',
            'photos.*' => 'Only image files are allowed in photos field',

            'description.required' => 'A description is required',
            'description.string' => 'The description field is not valid',
            'description.max' => 'Can not have more than 250 characters',
        ];
    }
}
