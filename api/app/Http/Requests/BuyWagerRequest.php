<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Wager;
use Illuminate\Foundation\Http\FormRequest;

class BuyWagerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'buying_price' => [
                'required',
                'decimal:2',
                'min:0.01',
                function ($attribute, $value, $fail) {
                    $wager = Wager::findOrFail($this->route('wager_id'));
                    if ($value > $wager->current_selling_price) {
                        $fail('The buying price cannot be greater than the current selling price.');
                    }
                },
            ],
        ];
    }
}
