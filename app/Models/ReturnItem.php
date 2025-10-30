<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    public const STATUS_REQUESTED = 'requested';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_RECEIVED = 'received';

    protected $fillable = [
        'order_item_id',
        'quantity',
        'reason',
        'status',
        'requested_at',
        'requested_by',
        'processed_at',
        'processed_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeRequested($query)
    {
        return $this->scopeStatus($query, self::STATUS_REQUESTED);
    }

    public function scopeApproved($query)
    {
        return $this->scopeStatus($query, self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $this->scopeStatus($query, self::STATUS_REJECTED);
    }

    public function scopeReceived($query)
    {
        return $this->scopeStatus($query, self::STATUS_RECEIVED);
    }
}
