<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserMultipleRequest extends FormRequest
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
            'users' => 'required|array|max:25',
            'users.*' => 'required_unless:users,'.null.'|uuid|max:100',
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
            'users.required' => 'A user is required',
            'users.array' => 'Users selection must be in the required type',
            'users.max' => 'Users selection can not have more than 25 items',
            'users.*' => 'One of the user selection item is either not valid or has more than 100 characters',
        ];
    }
}
