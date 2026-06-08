<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCustomerRequest extends FormRequest
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
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:customers,email,' . $this->route('id'),
            'phone' => 'nullable|string|max:20',
            'status' => 'nullable|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The customer name is required.',
            'name.string' => 'The customer name must be a valid string.',
            'email.required' => 'The customer email is required.',
            'email.email' => 'The customer email must be a valid email address.',
            'email.unique' => 'This email is already used by another customer.',
            'phone.string' => 'The phone number must be a valid string.',
            'status.in' => 'The status must be either active or inactive.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422));
    }
}
