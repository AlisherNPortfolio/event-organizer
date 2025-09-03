<?php

namespace App\Presentation\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UploadPhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048']
        ];
    }

    public function messages(): array
    {
        return [
            'photo.required' => 'Rasm yuklash majburiy',
            'photo.image' => 'Faqat rasm fayllarini yuklash mumkin',
            'photo.mimes' => 'Faqat jpeg, png va jpg formatidagi rasmlarni yuklash mumkin',
            'photo.max' => 'Rasm hajmi 2mb dan oshmasligi kerak'
        ];
    }
}
