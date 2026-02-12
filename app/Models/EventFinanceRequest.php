<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventFinanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'logistics_total',
        'equipment_total',
        'grand_total',
        'status',
        'submitted_by',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
}
