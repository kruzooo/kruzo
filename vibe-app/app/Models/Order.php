<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'email',
        'first_name',
        'last_name',
        'phone',
        'address',
        'barangay',
        'city',
        'province',
        'postal_code',
        'payment_method',
        'status',
        'subtotal',
        'cart',
        'placed_at',
    ];

    protected function casts(): array
    {
        return [
            'cart' => 'array',
            'subtotal' => 'decimal:2',
            'placed_at' => 'datetime',
        ];
    }
}
