<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PastQuestionUpdateRequest extends FormRequest
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
            'department' => 'sometimes|required|string|max:100',
            'course_name' => 'sometimes|required|string|max:100',
            'course_code' => 'sometimes|required|string|max:25',
            'semester' => 'sometimes|required|string|max:25',
            'year' => 'sometimes|required|integer|digits_between:4,4',
            'photos' => 'sometimes|required_if:docs,'.null.'|array|max:10|filled',
            'docs' => 'sometimes|required_if:photos,'.null.'|array|max:10|filled',
            'photos.*' => 'required_unless:photos,'.null.'|image',
            'docs.*' => 'required_unless:docs,'.null.'|file',
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

            'department.required' => 'A department is required',
            'department.string' => 'Department characters are not valid',
            'department.max' => 'A department can not have more than 100 characters',

            'course_name.required' => 'A course name is required',
            'course_name.string' => 'Course name characters are not valid',
            'course_name.max' => 'A course name can not have more than 100 characters',

            'course_code.required' => 'A course code maybe required',
            'course_code.string' => 'Course code characters are not valid',
            'course_code.max' => 'A course code can not have more than 25 characters',

            'semester.required' => 'A semester maybe required',
            'semester.string' => 'Semester characters are not valid',
            'semester.max' => 'A semester can not have more than 25 characters',

            'year.required' => 'A year is required',
            'year.string' => 'Year characters are not valid',
            'year.digits_between' => 'A year can not have more than or less than 4 characters',

            'photos.required_if' => 'A photo or document is required',
            'photos.array' => 'The photos field is not valid',
            'photos.max' => 'Can not have more than 10 photos',
            'photos.filled' => 'The photos field is actually empty',
            'photos.*' => 'Only image files are allowed in photos field',

            'docs.required_if' => 'A photo or document is required',
            'docs.array' => 'The documents field is not valid',
            'docs.max' => 'Can not have more than 10 documents',
            'docs.filled' => 'The documents field is actually empty',
            'docs.*' => 'Only document files such as MS word, PDF, Txt, etc. are allowed in documents field',
        ];
    }
}
