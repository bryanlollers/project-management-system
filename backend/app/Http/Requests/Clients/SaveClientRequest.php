<?php

namespace App\Http\Requests\Clients;

use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class SaveClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->isMethod('post') ? Gate::allows('create', Client::class) : Gate::allows('update', $this->route('client'));
    }

    public function rules(): array
    {
        $required = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'name' => [$required, 'string', 'max:160'],
            'company' => ['nullable', 'string', 'max:160'],
            'email' => [$required, 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'contacts' => ['nullable', 'array', 'max:30'],
            'contacts.*.name' => ['required', 'string', 'max:160'],
            'contacts.*.email' => ['required', 'email'],
            'contacts.*.phone' => ['nullable', 'string', 'max:40'],
        ];
    }
}
