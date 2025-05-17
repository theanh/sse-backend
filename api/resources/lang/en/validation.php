<?php

declare(strict_types=1);

return [
    'required' => 'The :attribute field is required.',
    'integer' => 'The :attribute must be an integer.',
    'min' => [
        'numeric' => 'The :attribute must be at least :min.'
    ],
    'between' => [
        'numeric' => 'The :attribute must be between :min and :max.',
    ],
    'decimal' => 'The :attribute must be a decimal with :decimal places.',
    'gt' => 'The :attribute must be greater than :value.',
    'custom' => [
        'selling_price' => [
            'min_price' => 'The selling price must be greater than :min.',
        ],
    ],
    'attributes' => [
        'total_wager_value' => 'total wager value',
        'odds' => 'odds',
        'selling_percentage' => 'selling percentage',
        'selling_price' => 'selling price',
    ],
];
