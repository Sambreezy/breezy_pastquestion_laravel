<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthResetPasswordRequest extends FormRequest
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
            'email' => 'required|email|max:100',
            'key' => 'required',
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
            'email.required' => 'An email is required',
            'email.max' => 'An email can not have more than 100 characters',
            'email.email'  => 'Email is not valid',

            'key.required'  => 'A reset key is required',

            'new_password.required'  => 'A new password is required',
            'new_password.confirmed'  => 'A new password confirmation is required',
            'new_password_confirmation.required'  => 'A new password confirmation is required',
            'new_password_confirmation.same'  => 'Passwords do not match',
        ];
    }
}
