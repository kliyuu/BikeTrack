<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
  protected $fillable = [
    'client_id',
    'placed_by',
    'order_number',
    'status',
    'total_amount',
    'tax_amount',
    'shipping_amount',
    'shipping_address',
    'billing_address',
    'notes',
    'placed_at',
    'shipped_at',
    'delivered_at',
    'cancelled_at',
  ];

  protected $casts = [
    'total_amount' => 'decimal:2',
    'tax_amount' => 'decimal:2',
    'shipping_amount' => 'decimal:2',
    'placed_at' => 'datetime',
    'shipped_at' => 'datetime',
    'delivered_at' => 'datetime',
    'cancelled_at' => 'datetime',
  ];

  public function client()
  {
    return $this->belongsTo(Client::class);
  }

  public function placedBy()
  {
    return $this->belongsTo(User::class, 'placed_by');
  }

  public function items()
  {
    return $this->hasMany(OrderItem::class);
  }

  public function payments()
  {
    return $this->hasMany(Payment::class);
  }

  public function shipments()
  {
    return $this->hasMany(Shipment::class);
  }
}
