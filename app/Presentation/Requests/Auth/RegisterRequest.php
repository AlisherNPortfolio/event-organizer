<?php

namespace App\Presentation\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'password_confirmation' => ['required', 'string', 'same:password'],
            'captcha' => ['required', 'captcha'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Ism kiritish majburiy',
            'email.required' => 'Email kiritish majburiy',
            'email.email' => 'Email formati noto\'g\'ri',
            'email.unique' => 'Bu email allaqachon ro\'yxatdan o\'tgan',
            'password.required' => 'Parol kiritish majburiy',
            'password.min' => 'Parol kamida 8 ta belgidan iborat bo\'lishi kerak',
            'password_confirmation.same' => 'Parol tasdiqlash mos kelmaydi',
            'captcha.required' => 'Captcha kodi kiritish majburiy',
            'captcha.captcha' => 'Captcha kodi noto\'g\'ri',
        ];
    }
}
