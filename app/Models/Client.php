<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
  use HasFactory, SoftDeletes;

  protected $fillable = [
    'code',
    'company',
    'contact_name',
    'contact_phone',
    'contact_email',
    'tax_number',
    'payment_method',
    'billing_address',
    'shipping_address',
    'status',
    'is_active',
  ];

  protected $casts = [
    'is_active' => 'boolean'
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function orders()
  {
    return $this->hasMany(Order::class);
  }
}
