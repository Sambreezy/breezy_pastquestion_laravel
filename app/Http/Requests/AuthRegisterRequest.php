<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRegisterRequest extends FormRequest
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
            'name' => 'sometimes|required|string|max:25',
            'email' => 'required|email|max:100',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required|same:password',
            'phone' => 'sometimes|required|string|max:25',
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
            'name.required' => 'A name maybe required',
            'name.string' => 'Name characters are not valid',
            'name.max' => 'A name can not have more than 25 characters',
            'email.required' => 'An email is required',
            'email.email' => 'Email is not valid',
            'email.max' => 'An email can not have more than 100 characters',
            'password.required'  => 'A password is required',
            'password.confirmed'  => 'A password confirmation is required',
            'password_confirmation.required'  => 'A password confirmation is required',
            'password_confirmation.same'  => 'Passwords do not match',
            'phone.required' => 'A phone number maybe required',
            'phone.string' => 'Phone number characters are not valid',
            'phone.max' => 'A phone number can not have more than 25 characters',
        ];
    }
}
