<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
  protected $fillable = [
    'order_item_id',
    'quantity',
    'reason',
    'processed_by',
    'status',
  ];

  public function orderItem()
  {
    return $this->belongsTo(OrderItem::class);
  }

  public function processedBy()
  {
    return $this->belongsTo(User::class, 'processed_by');
  }
}
