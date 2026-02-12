<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// 1. ADD THIS CORRECT IMPORT:
use Illuminate\Database\Eloquent\Relations\BelongsTo; 

class EventCustodianRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'custodian_material_id',
        'quantity',
        'status',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    // 2. ENSURE THE RETURN TYPE IS BelongsTo
    public function custodianMaterial(): BelongsTo
    {
        return $this->belongsTo(CustodianMaterial::class, 'custodian_material_id');
    }
}