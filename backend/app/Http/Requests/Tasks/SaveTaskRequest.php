<?php

namespace App\Http\Requests\Tasks;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class SaveTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->isMethod('post') ? Gate::allows('create', Task::class) : Gate::allows('update', $this->route('task'));
    }

    public function rules(): array
    {
        $required = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'title' => [$required, 'string', 'max:160'],
            'project_id' => [$required, 'exists:projects,id'],
            'assignee_id' => ['nullable', 'exists:users,id'],
            'description' => ['nullable', 'string', 'max:10000'],
            'status' => ['sometimes', Rule::in(['todo', 'in_progress', 'review', 'done'])],
            'priority' => ['sometimes', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'due_date' => ['nullable', 'date'],
        ];
    }

    protected function passedValidation(): void
    {
        if (! $this->user()->manages()) {
            abort_if(count(array_diff(array_keys($this->validated()), ['status'])) > 0, 403);
        }
    }
}
