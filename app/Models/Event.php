<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Event extends Model
{
    public const STATUS_PENDING_APPROVAL = 'pending_approval';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'title',
        'description',
        'start_at',
        'end_at',
        'status',
        'requested_by',
        'venue_id',              // ADDED: Critical for saving venue
        'is_venue_approved',     // ADDED: For approval gate
        'is_logistics_approved', // ADDED: For approval gate
        'is_finance_approved',   // ADDED: For approval gate
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'is_venue_approved' => 'boolean',
            'is_logistics_approved' => 'boolean',
            'is_finance_approved' => 'boolean',
        ];
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function venueBookings(): HasMany
    {
        return $this->hasMany(VenueBooking::class);
    }

public function resourceAllocations(): HasMany
{
    return $this->hasMany(ResourceAllocation::class, 'event_id', 'id');
}


    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function budget(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function programItems(): HasMany
    {
        return $this->hasMany(ProgramItem::class)->orderBy('order');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(EventMedia::class);
    }

    public function feedbackResponses(): HasMany
    {
        return $this->hasMany(FeedbackResponse::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(EventHistory::class)->orderBy('created_at', 'asc');
    }

    public function custodianRequests(): HasMany
    {
        return $this->hasMany(EventCustodianRequest::class);
    }

    public function custodianMaterials()
    {
        return $this->belongsToMany(
            CustodianMaterial::class,
            'event_custodian_requests'
        )->withPivot(['quantity', 'status'])->withTimestamps();
    }

    public function financeRequest(): HasOne
    {
        return $this->hasOne(EventFinanceRequest::class);
    }
}