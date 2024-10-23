<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'user_name' => 'required|unique:users,user_name|regex:/^[A-Za-z0-9-]+$/',
            'password' => 'required|regex:/^\w{8,}$/|confirmed',
        ];
    }
}
