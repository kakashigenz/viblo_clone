<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

class LoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    #[
        OA\Schema(
            schema: 'LoginRequest',
            properties: [
                new OA\Property(property: 'user_name', type: 'string'),
                new OA\Property(property: 'password', type: 'string')
            ]
        )
    ]
    public function rules(): array
    {
        return [
            'user_name' => 'required',
            'password' => 'required'
        ];
    }
}
