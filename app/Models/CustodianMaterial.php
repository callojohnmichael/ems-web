<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustodianMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'stock',
    ];

    public function requests()
    {
        return $this->hasMany(EventCustodianRequest::class);
    }
}
