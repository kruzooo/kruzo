<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = ['sku', 'name', 'stock', 'low_stock_threshold', 'price', 'image'];

    protected function casts(): array
    {
        return ['stock' => 'integer', 'low_stock_threshold' => 'integer', 'price' => 'decimal:2'];
    }
}
