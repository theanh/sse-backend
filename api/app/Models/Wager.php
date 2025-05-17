<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wager extends Model
{
    protected $fillable = [
        'total_wager_value',
        'odds',
        'selling_percentage',
        'selling_price',
        'current_selling_price',
        'percentage_sold',
        'amount_sold',
        'placed_at',
    ];

    protected $casts = [
        'total_wager_value' => 'integer',
        'odds' => 'integer',
        'selling_percentage' => 'integer',
        'selling_price' => 'decimal:2',
        'current_selling_price' => 'decimal:2',
        'percentage_sold' => 'decimal:2',
        'amount_sold' => 'decimal:2',
        'placed_at' => 'datetime',
    ];

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }
}
