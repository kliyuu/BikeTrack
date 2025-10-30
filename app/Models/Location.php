<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'floor',
        'aisle',
        'shelf',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
