<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LiteratureRequest extends Model
{
    protected $fillable = [
        'group_id',
        'service_committee_id',
        'service_body_id',
        'month',
        'status', // draft, submitted, sent_to_committee, returned_by_committee, dispatched, completed, returned
        'type', // group, servicebody, committee
        'total_items_count',
        'total_price',
    ];

    protected $casts = [
        'month' => 'date',
        'total_price' => 'decimal:2',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function serviceCommittee(): BelongsTo
    {
        return $this->belongsTo(ServiceCommittee::class);
    }

    public function serviceBody(): BelongsTo
    {
        return $this->belongsTo(ServiceBody::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(LiteratureRequestItem::class);
    }

    public function slips(): HasMany
    {
        return $this->hasMany(InventorySlip::class);
    }

    public function isCommitteeRequest(): bool
    {
        return $this->type === 'committee';
    }

    public function deliverySlip(): ?InventorySlip
    {
        return $this->slips()->where('type', 'issue_to_committee')->latest()->first();
    }

    public function returnSlips(): HasMany
    {
        return $this->slips()->where('type', 'return_from_committee');
    }
}

