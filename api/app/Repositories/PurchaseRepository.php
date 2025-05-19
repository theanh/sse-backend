<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Purchase;
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

    public function create(int $wagerId, float $buyingPrice): Purchase
    {
        return $this->model->create([
            'wager_id' => $wagerId,
            'buying_price' => $buyingPrice,
            'bought_at' => Carbon::now(),
        ]);
    }

    public function buy(int $wagerId, float $buyingPrice): Purchase
    {
        $this->db->beginTransaction();
        try {
            // Reload the latest wager info inside the transaction
            $wager = $this->wagerRepository->findOne($wagerId);
            if (!$wager) {
                throw new \RuntimeException('Wager not found.');
            }
            // Validate buying price against current_selling_price inside transaction
            if ($buyingPrice > $wager->current_selling_price) {
                throw new \RuntimeException('The buying price must be less than or equal to the current selling price.');
            }
            $purchase = $this->create($wager->id, $buyingPrice);
            $this->wagerRepository->updatePurchaseStats($wager, $buyingPrice);

            $this->db->commit();

            return $purchase;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
