<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;


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
            'name' => 'sometimes|required|string|max:100|unique:App\Models\User,name',
            'email' => 'required|email|max:100',
            'password' => 'required|min:6|max:24|confirmed',
            'password_confirmation' => 'required|same:password',
            'phone' => 'sometimes|required|string|max:25',
            'description' => 'string|max:225',
            'birth_date' => 'string|max:11',
            'birth_year' => 'string|max:4',
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
            'name.sometimes' => 'A name should be present, else entirely exclude the field',
            'name.required' => 'A name maybe required',
            'name.string' => 'Name characters are not valid',
            'name.max' => 'A name can not have more than 100 characters',
            'name.unique' => 'The name has already been taken',
            'email.required' => 'An email is required',
            'email.email' => 'Email is not valid',
            'email.max' => 'An email can not have more than 100 characters',
            'password.required'  => 'A password is required',
            'password.min'  => 'Password must have a minimum of 6 characters',
            'password.max'  => 'Password must have a maximum of 24 characters',
            'password.confirmed'  => 'A password confirmation is required',
            'password_confirmation.required'  => 'A password confirmation is required',
            'password_confirmation.same'  => 'Passwords do not match',
            'phone.sometimes' => 'A phone should be present, else entirely exclude the field',
            'phone.required' => 'A phone number maybe required',
            'phone.string' => 'Phone number characters are not valid',
            'phone.max' => 'A phone number can not have more than 25 characters',
            'description.max' => 'A Description can not have more than 225 characters',
            'description.string' => 'Description characters are not valid',
            'birth_date.max' => 'Birth date can not have more than 25 characters',
            'birth_date.string' => 'Birth date characters are not valid',
            'birth_year.max' => 'Birth year can not have more than 4 characters',
            'birth_year.string' => 'Birth year characters are not valid',
        ];
    }
}
