<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthChangePasswordRequest extends FormRequest
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
            'old_password' => 'required',
            'new_password' => 'required|confirmed',
            'new_password_confirmation' => 'required|same:new_password',
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
            'id.max' => 'An id can not have more than 100 characters',

            'old_password.required'  => 'Old password is required',

            'new_password.required'  => 'A new password is required',
            'new_password.confirmed'  => 'A new password confirmation is required',
            'new_password_confirmation.required'  => 'A new password confirmation is required',
            'new_password_confirmation.same'  => 'Passwords do not match',
        ];
    }
}
