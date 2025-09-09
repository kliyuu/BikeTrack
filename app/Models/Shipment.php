<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
  protected $fillable = [
    'order_id',
    'warehouse_id',
    'tracking_number',
    'carrier',
    'shipped_at',
    'expected_delivery_at',
    'status'
  ];

  protected $dates = [
    'shipped_at',
    'expected_delivery_at'
  ];

  public function order()
  {
    return $this->belongsTo(Order::class);
  }

  public function warehouse()
  {
    return $this->belongsTo(Warehouse::class);
  }
}
