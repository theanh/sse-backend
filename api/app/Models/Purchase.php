<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Purchase extends Model
{
    protected $fillable = [
        'wager_id',
        'buying_price',
        'bought_at',
    ];

    protected $casts = [
        'buying_price' => 'decimal:2',
        'bought_at' => 'datetime',
    ];

    public function wager(): BelongsTo
    {
        return $this->belongsTo(Wager::class);
    }
}
