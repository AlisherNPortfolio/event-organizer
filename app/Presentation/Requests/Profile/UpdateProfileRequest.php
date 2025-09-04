<?php

namespace App\Presentation\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'min:2'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore(Auth::id())],
            'current_password' => [
                'nullable',
                'required_with:password',
                'string',
                'min:8'
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
                'different:current_password'
            ],
            'password_confirmation' => [
                'nullable',
                'required_with:password',
                'string',
                'min:8'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Ism kiritish majburiy',
            'email.required' => 'Email kiritish majburiy',
            'email.email' => 'Email formati noto\'g\'ri',
            'email.unique' => 'Bu email allaqachon ishlatilmoqda',
            'phone.max' => 'Telefon raqami 20 ta belgidan oshmasligi kerak',
            'bio.max' => 'Biografiya 500 ta belgidan oshmasligi kerak',
            'location.max' => 'Joylashuv 255 ta belgidan oshmasligi kerak',
            'website.url' => 'Website URL formati noto\'g\'ri',
            'website.max' => 'Website URL 255 ta belgidan oshmasligi kerak',
            'current_password.required_with' => 'Parolni o\'zgartirish uchun joriy parolni kiriting',
            'current_password.min' => 'Joriy parol kamida 8 ta belgidan iborat bo\'lishi kerak',
            'password.min' => 'Yangi parol kamida 8 ta belgidan iborat bo\'lishi kerak',
            'password.confirmed' => 'Parol tasdiqlash mos kelmaydi',
            'password.different' => 'Yangi parol joriy paroldan farqli bo\'lishi kerak',
            'password_confirmation.required_with' => 'Parol tasdiqlash majburiy',
            'password_confirmation.min' => 'Parol tasdiqlash kamida 8 ta belgidan iborat bo\'lishi kerak',
        ];
    }
}
