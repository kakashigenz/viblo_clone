<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;

class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    #[OA\Schema(
        schema: 'RegisterRequest',
        properties: [
            new OA\Property(property: 'name', type: 'string', example: "John Doe"),
            new OA\Property(property: 'email', type: 'string', example: "john.doe@example.com"),
            new OA\Property(property: 'user_name', type: 'string', example: "john-doe"),
            new OA\Property(property: 'password', type: 'string', example: "password123"),
            new OA\Property(property: 'password_confirmation', type: 'string', example: "password123")
        ]
    )]
    public function rules(): array
    {
        return [
            'name' => 'required',
            'email' => 'required|unique:users,email|email',
            'user_name' => 'required|unique:users,user_name|regex:/^[A-Za-z0-9-]+$/',
            'password' => 'required|regex:/^\w{8,}$/|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => 'Địa chỉ Email đã được đăng ký.',
            'user_name.unique' => 'Tên tài khoản đã được sử dụng',
            'user_name.regex' => 'Tên tài khoản không hợp lệ',
            'password.regex' => 'Mật khẩu không hợp lệ.',
        ];
    }
}
