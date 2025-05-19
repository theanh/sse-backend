<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Wager;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class WagerRepository
{
    public function __construct(
        protected Wager $wager
    ) {
    }

    public function create(array $data): Wager
    {
        return $this->wager->create([
            ...$data,
            'current_selling_price' => $data['selling_price'],
            'placed_at' => Carbon::now(),
        ]);
    }

    public function list(int $page = 1, int $limit = 10): LengthAwarePaginator
    {
        return $this->wager->orderBy('id', 'desc')
            ->paginate(
                perPage: $limit,
                page: $page
            );
    }

    public function findOne(int $id): ?Wager
    {
        return $this->wager->find($id);
    }

    public function updatePurchaseStats(Wager $wager, float $buyingPrice): void
    {
        $wager->current_selling_price = $wager->current_selling_price - $buyingPrice;
        $wager->amount_sold = ($wager->amount_sold ?? 0) + $buyingPrice;
        $wager->percentage_sold = ($wager->amount_sold / $wager->selling_price) * 100;
        $wager->save();
    }
}
