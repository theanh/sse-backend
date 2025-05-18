<?php

declare(strict_types=1);

namespace App\Http\Resources;

class PurchaseResource extends ApiResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'wager_id' => $this->wager_id,
            'buying_price' => (float) $this->buying_price,
            'bought_at' => $this->bought_at ? $this->bought_at->toISOString() : null,
        ];
    }
}
