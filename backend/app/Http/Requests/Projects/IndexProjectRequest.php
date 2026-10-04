<?php

namespace App\Http\Requests\Projects;

use App\Http\Requests\Shared\PaginatedRequest;

class IndexProjectRequest extends PaginatedRequest
{
    public function rules(): array
    {
        return parent::rules() + ['status' => 'nullable|string', 'priority' => 'nullable|string', 'client_id' => 'nullable|integer'];
    }
}
