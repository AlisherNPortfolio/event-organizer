<?php

namespace App\Presentation\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateAvatarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.required' => 'Avatar rasmi kiritish majburiy',
            'avatar.image' => 'Faqat rasm fayllari yuklash mumkin',
            'avatar.mimes' => 'Faqat JPEG, PNG, JPG, GIF formatdagi fayllar qabul qilinadi',
            'avatar.max' => 'Rasm hajmi 2MB dan oshmasligi kerak',
        ];
    }
}
