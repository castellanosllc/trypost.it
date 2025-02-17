<?php

declare(strict_types=1);

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'min:2'],
            // 'scheduled_at' => ['required', 'date', 'after:now'],
            'accounts' => ['required', 'array'],
            'accounts.*' => ['required', 'exists:accounts,id'],
        ];
    }
}
