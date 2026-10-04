<?php

namespace App\Http\Requests\Comments;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('view', $this->route('task'));
    }

    public function rules(): array
    {
        return ['body' => 'required|string|max:5000'];
    }
}
