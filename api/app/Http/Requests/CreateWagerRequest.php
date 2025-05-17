<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateWagerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'total_wager_value' => ['bail', 'required', 'integer', 'min:1'],
            'odds' => ['bail', 'required', 'integer', 'min:1'],
            'selling_percentage' => ['bail', 'required', 'integer', 'between:1,100'],
            'selling_price' => [
                'bail',
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) {
                    $minPrice = $this->input('total_wager_value')
                        * ($this->input('selling_percentage') / 100);
                    if ($value <= $minPrice) {
                        $fail(
                            __('validation.custom.selling_price.min_price', [
                                'min' => $minPrice,
                            ])
                        );
                    }
                },
            ],
        ];
    }
}
