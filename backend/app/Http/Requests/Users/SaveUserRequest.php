<?php

namespace App\Http\Requests\Users;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class SaveUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->isMethod('post') ? Gate::allows('create', User::class) : Gate::allows('update', $this->route('user'));
    }

    public function rules(): array
    {
        $required = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'name' => [$required, 'string', 'max:160'],
            'email' => [$required, 'email', Rule::unique('users')->ignore($this->route('user')?->id)],
            'password' => [$required, 'string', 'min:12', 'max:128'],
            'role' => [$required, Rule::in(['admin', 'manager', 'staff'])],
        ];
    }
}
