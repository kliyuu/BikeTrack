<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
  protected $fillable = [
    'order_id',
    'product_id',
    'warehouse_id',
    'quantity',
    'unit_price',
    'line_total',
  ];

  public function order()
  {
    return $this->belongsTo(Order::class);
  }

  public function product()
  {
    return $this->belongsTo(Product::class);
  }

  public function warehouse()
  {
    return $this->belongsTo(Warehouse::class);
  }

  public function returns()
  {
    return $this->hasMany(ReturnItem::class);
  }
}
