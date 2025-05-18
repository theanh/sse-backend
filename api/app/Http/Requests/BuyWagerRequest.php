<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Repositories\WagerRepository;
use Illuminate\Foundation\Http\FormRequest;

class BuyWagerRequest extends FormRequest
{
    public function __construct(
        protected WagerRepository $wagerRepository
    ) {
        parent::__construct();
    }

    public function rules(): array
    {
        return [
            'buying_price' => [
                'bail',
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) {
                    $wagerId = (int) $this->route('wager_id');
                    $wager = $this->wagerRepository->findOne($wagerId);
                    if (empty($wager)) {
                        $fail(__('validation.not_found'));
                        return;
                    }

                    if ($value > $wager->current_selling_price) {
                        $fail(__('validation.custom.buying_price.max',
                            ['max' => $wager->current_selling_price]
                        ));
                    }
                },
            ],
        ];
    }
}
