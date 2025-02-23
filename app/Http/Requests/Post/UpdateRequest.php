<?php

declare(strict_types=1);

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\Post\Status;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => [new Enum(Status::class)],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'auto_sync' => ['required', 'boolean'],
        ];
    }
}
