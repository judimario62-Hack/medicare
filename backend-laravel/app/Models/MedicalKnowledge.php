<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalKnowledge extends Model
{
    protected $table = 'medical_knowledge';

    protected $fillable = [
        'title',
        'category',
        'content',
        'tags',
        'source',
        'is_active',
        'embedding',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'is_active' => 'boolean',
            'embedding' => 'array',
        ];
    }
}
