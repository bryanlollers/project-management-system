<?php

namespace App\Http\Requests\Tasks;

use App\Http\Requests\Shared\PaginatedRequest;

class IndexTaskRequest extends PaginatedRequest
{
    public function rules(): array
    {
        return parent::rules() + ['status' => 'nullable|string', 'priority' => 'nullable|string', 'project_id' => 'nullable|integer', 'assignee_id' => 'nullable|integer'];
    }
}
