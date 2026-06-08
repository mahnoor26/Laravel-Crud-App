<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UploadCustomerFileRequest extends FormRequest
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
            'file' => 'required|file|max:51200', // 50MB max
            'customer_id' => 'required|exists:customers,id',
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'A file is required.',
            'file.file' => 'The uploaded item must be a file.',
            'file.max' => 'The file size must not exceed 50MB.',
            'customer_id.required' => 'The customer ID is required.',
            'customer_id.exists' => 'The selected customer does not exist.',
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
