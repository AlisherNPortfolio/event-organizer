<?php

namespace App\Presentation\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:2000'],
            'address' => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'date', 'after:now'],
            'end_time' => ['nullable', 'date', 'after:start_time'],
            'min_participants' => ['required', 'integer', 'min:1'],
            'max_participants' => ['nullable', 'integer', 'gte:min_participants'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable','string', 'in:UZS,USD,EUR'],
            'image' => ['nullable','image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tadbir nomi kiritish majburiy',
            'title.max' => 'Tadbir nomi 100 ta belgidan oshmasligi kerak',
            'description.required' => 'Tadbir tavsifi kiritish majburiy',
            'description.max' => 'Tadbir tavsifi 2000 ta belgidan oshmasligi kerak',
            'address.required' => 'Manzil kiritish majburiy',
            'start_time.required' => 'Boshlanish vaqti kiritish majburiy',
            'start_time.after' => 'Boshlanish vaqti hozirgi vaqtdan keyin bo\'lishi kerak',
            'end_time.after' => 'Tugash vaqti boshlanish vaqtidan keyin bo\'lishi kerak',
            'min_participants.required' => 'Minimal qatnashchilar soni kiritish majburiy',
            'min_participants.min' => 'Minimal qatnashchilar soni 1 dan kam bo\'lmasligi kerak',
            'max_participants.gte' => 'Maksimal qatnashchilar soni minimal sondan kam bo\'lmasligi kerak',
            'image.max' => 'Rasm hajmi 2MB dan oshmasligi kerak'
        ];
    }
}
