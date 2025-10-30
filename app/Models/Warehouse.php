<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'location',
        'description',
        'contact_person',
        'contact_number',
        'contact_email',
    ];

    public function inventoryLevels()
    {
        return $this->hasMany(InventoryLevel::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function restockHistories()
    {
        return $this->hasMany(RestockHistory::class);
    }
}
