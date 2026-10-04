<?php

namespace App\Http\Requests\Projects;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SaveProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->isMethod('post') ? Gate::allows('create', Project::class) : Gate::allows('update', $this->route('project'));
    }

    public function rules(): array
    {
        $required = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'name' => [$required, 'string', 'max:160'],
            'client_id' => [$required, 'exists:clients,id'],
            'description' => ['nullable', 'string', 'max:10000'],
            'status' => ['sometimes', Rule::in(['planning', 'active', 'on_hold', 'completed'])],
            'priority' => ['sometimes', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'member_ids' => ['sometimes', 'array'],
            'member_ids.*' => ['integer', 'distinct', 'exists:users,id'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $project = $this->route('project');
            $start = $this->exists('start_date') ? $this->input('start_date') : $project?->start_date?->format('Y-m-d');
            $end = $this->exists('end_date') ? $this->input('end_date') : $project?->end_date?->format('Y-m-d');
            if ($start && $end && strtotime($end) < strtotime($start)) {
                $validator->errors()->add('end_date', 'The end date must be on or after the start date.');
            }
        }];
    }
}
