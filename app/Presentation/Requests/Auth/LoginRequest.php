<?php

namespace App\Presentation\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'captcha' => ['required', 'captcha'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email kiritish majburiy',
            'email.email' => 'Email formati noto\'g\'ri',
            'password.required' => 'Parol kiritish majburiy',
            'captcha.required' => 'Captcha kodi kiritish majburiy',
            'captcha.captcha' => 'Captcha kodi noto\'g\'ri'
        ];
    }
}
