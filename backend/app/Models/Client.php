<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'company', 'notes', 'contacts'];

    protected function casts(): array
    {
        return ['contacts' => 'array'];
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
