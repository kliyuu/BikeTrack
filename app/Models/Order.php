<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
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

    public const STATUSES = [
        'pending',
        'confirmed',
        'processing',
        'shipped',
        'delivered',
        'completed',
        'cancelled',
        'returned',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::updating(function ($order) {
            // Automatically set delivered_at when status changes to delivered
            if ($order->isDirty('status') && $order->status === 'delivered' && is_null($order->delivered_at)) {
                $order->delivered_at = now();
            }

            // Automatically set shipped_at when status changes to shipped
            if ($order->isDirty('status') && $order->status === 'shipped' && is_null($order->shipped_at)) {
                $order->shipped_at = now();
            }

            // Automatically set cancelled_at when status changes to cancelled
            if ($order->isDirty('status') && $order->status === 'cancelled' && is_null($order->cancelled_at)) {
                $order->cancelled_at = now();
            }
        });
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
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

    public function scopeSearch($query, $search)
    {
        if (! $search) {
            return;
        }

        // $searchFields = [
        //   'orders' => ['order_number'],
        //   'clients' => ['company_name', 'contact_name'],
        //   'users' => ['name'],
        // ];

        // $query->where(function ($q) use ($search, $searchFields) {
        //   // Search in orders table | main query
        //   foreach ($searchFields['orders'] as $field) {
        //     $q->where("orders.$field", 'like', "%{$search}%");
        //   }

        //   // Search in clients table | related model
        //   $q->orWhereHas('client', function ($clientQuery) use ($search, $searchFields) {
        //     foreach ($searchFields['clients'] as $field) {
        //       $clientQuery->orWhere($field, 'like', "%{$search}%");
        //     }
        //   });

        //   // Search in users table | related model through client
        //   $q->orWhereHas('client.user', function ($userQuery) use ($search, $searchFields) {
        //     foreach ($searchFields['users'] as $field) {
        //       $userQuery->orWhere($field, 'like', "%{$search}%");
        //     }
        //   });
        // });

        $query->where(function ($q) use ($search) {
            $q->where('orders.order_number', 'like', "%{$search}%")
                ->orWhereHas('client', function ($clientQuery) use ($search) {
                    $clientQuery->where('company_name', 'like', "%{$search}%")
                        ->orWhere('contact_name', 'like', "%{$search}%");
                });
        });
    }
}
