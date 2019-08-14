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
            'images' => 'required|array|max:25',
            'images.*' => 'required_unless:images,'.null.'|uuid|max:100',
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
            'images.required' => 'An image is required',
            'images.array' => 'Images selection must be in the required type',
            'images.max' => 'Images selection can not have more than 25 items',
            'images.*' => 'One of the image selection item is either not valid or has more than 100 characters',
        ];
    }
}
