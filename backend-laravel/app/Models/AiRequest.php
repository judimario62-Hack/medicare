<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class AiRequest extends Model
{
    protected $fillable = [
        'requested_by',
        'patient_id',
        'consultation_id',
        'type',
        'status',
        'input_data',
        'attempts',
        'claimed_at',
        'completed_at',
        'failure_code',
    ];

    protected function casts(): array
    {
        return [
            'input_data' => 'array',
            'attempts' => 'integer',
            'claimed_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }

    public function summary(): HasOne
    {
        return $this->hasOne(AiSummary::class);
    }
}
