<?php

namespace App\Http\Requests\FileManagement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Sanctum auth handled by middleware
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'max:10240'],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(
                [
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ],
                422
            )
        );
    }
    
}

