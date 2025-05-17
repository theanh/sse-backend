<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Purchase;
use App\Models\Wager;
use Carbon\Carbon;

class PurchaseRepository
{
    public function create(Wager $wager, float $buyingPrice): Purchase
    {
        return Purchase::create([
            'wager_id' => $wager->id,
            'buying_price' => $buyingPrice,
            'bought_at' => Carbon::now(),
        ]);
    }
}
