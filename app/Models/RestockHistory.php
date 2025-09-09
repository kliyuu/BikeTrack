<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestockHistory extends Model
{
  // public $timestamps = false;

  protected $fillable = [
    'product_id',
    'warehouse_id',
    'quantity_before',
    'quantity_after',
    'quantity_change',
    'reason',
    'performed_by',
    'reference_type',
    'reference_id',
  ];

  public function product()
  {
    return $this->belongsTo(Product::class);
  }

  public function warehouse()
  {
    return $this->belongsTo(Warehouse::class);
  }

  public function performedBy()
  {
    return $this->belongsTo(User::class, 'performed_by');
  }
}
