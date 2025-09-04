<?php

namespace App\Presentation\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MarkAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'participant_id' => ['required', 'uuid'],
            'attended' => ['required', 'boolean']

        ];
    }

    public function messages(): array
    {
        return [
            'participant_id.required' => 'Qatnashning IDsi berilishi shart',
            'participant_id.uuid' => 'Qatnashchi IDsi UUID tipida bo\'lishi kerak',
            'attended.required' => 'attended parametri berilishi shart',
            'attended.boolean' => 'attended parametri boolean tipida bo\'lishi kerak'
        ];
    }
}
