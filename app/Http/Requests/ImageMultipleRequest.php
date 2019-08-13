<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImageMultipleRequest extends FormRequest
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
            'photos' => 'required|array|max:25',
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
            'photos.required' => 'An image is required',
            'photos.array' => 'Photos selection must be in the required type',
            'photos.max' => 'Photos selection can not have more than 25 items',
            'photos.*' => 'Only image files are allowed in photos field',
        ];
    }
}
