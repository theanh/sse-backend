<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Wager;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class WagerRepository
{
    public function create(array $data): Wager
    {
        return Wager::create([
            ...$data,
            'current_selling_price' => $data['selling_price'],
            'placed_at' => Carbon::now(),
        ]);
    }

    public function list(int $page = 1, int $limit = 10): LengthAwarePaginator
    {
        return Wager::orderBy('id', 'desc')
            ->paginate(
                perPage: $limit,
                page: $page
            );
    }

    public function findOrFail(int $id): Wager
    {
        return Wager::findOrFail($id);
    }

    public function updatePurchaseStats(Wager $wager, float $buyingPrice): void
    {
        $wager->current_selling_price = $wager->current_selling_price - $buyingPrice;
        $wager->amount_sold = ($wager->amount_sold ?? 0) + $buyingPrice;
        $wager->percentage_sold = ($wager->amount_sold / $wager->selling_price) * 100;
        $wager->save();
    }
}
