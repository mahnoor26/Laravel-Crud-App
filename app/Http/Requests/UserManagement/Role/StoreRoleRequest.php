<?php

namespace App\Http\Requests\UserManagement\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class StoreRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The role name is required.',
            'name.string' => 'The role name must be a valid string.',
            'name.max' => 'The role name may not be greater than 255 characters.',
            'name.unique' => 'This role name is already taken.',
            'description.string' => 'The description must be a valid string.',
            'guard_name.in' => 'The guard name must be web.',
            'permissions.array' => 'Permissions must be an array.',
            'permissions.*.exists' => 'One or more permissions do not exist.',
        ];
    }

    //! Handle the failed validation response in a consistent way across the application, maybe using a custom exception handler or a trait for API responses.
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(
                [
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ],
                400,
            ),
        );
    }
}
