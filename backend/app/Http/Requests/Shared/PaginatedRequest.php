<?php

namespace App\Http\Requests\Shared;

use Illuminate\Foundation\Http\FormRequest;

abstract class PaginatedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'per_page' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1',
            'search' => 'nullable|string|max:160',
        ];
    }
}
