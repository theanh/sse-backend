<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListWagersRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page' => [
                'bail',
                'sometimes',
                'integer',
                'min:1',
            ],
            'limit' => [
                'bail',
                'sometimes',
                'integer',
                'min:1',
                'max:20',
            ],
        ];
    }
}
