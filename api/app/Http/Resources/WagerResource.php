<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WagerResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'total_wager_value' => $this->total_wager_value,
            'odds' => $this->odds,
            'selling_percentage' => $this->selling_percentage,
            'selling_price' => (float) $this->selling_price,
            'current_selling_price' => (float) $this->current_selling_price,
            'percentage_sold' => $this->percentage_sold !== null ? (float) $this->percentage_sold : null,
            'amount_sold' => $this->amount_sold !== null ? (float) $this->amount_sold : null,
            'placed_at' => $this->placed_at ? $this->placed_at->toISOString() : null,
        ];
    }
}
