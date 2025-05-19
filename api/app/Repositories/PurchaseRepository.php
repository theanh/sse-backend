<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Purchase;
use App\Models\Wager;
use Carbon\Carbon;
use Illuminate\Database\DatabaseManager;

class PurchaseRepository
{
    public function __construct(
        protected Purchase $model,
        protected WagerRepository $wagerRepository,
        protected DatabaseManager $db
    ) {
    }

    public function create(Wager $wager, float $buyingPrice): Purchase
    {
        return $this->model->create([
            'wager_id' => $wager->id,
            'buying_price' => $buyingPrice,
            'bought_at' => Carbon::now(),
        ]);
    }

    public function buy(Wager $wager, float $buyingPrice): Purchase
    {
        return $this->db->transaction(function () use ($wager, $buyingPrice) {
            $purchase = $this->create($wager, $buyingPrice);
            $this->wagerRepository->updatePurchaseStats($wager, $buyingPrice);
            return $purchase;
        });
    }
}
