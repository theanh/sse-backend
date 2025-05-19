<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class PlaceWagerApiTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('validPayloadProvider')]
    public function testPlaceWagerSuccess(array $payload, array $expected): void
    {
        $response = $this->postJson('/api/wagers', $payload);

        $response->assertCreated();
        $this->assertSame(201, $response->status());
        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->hasAll([
                'id',
                'total_wager_value',
                'odds',
                'selling_percentage',
                'selling_price',
                'current_selling_price',
                'percentage_sold',
                'amount_sold',
                'placed_at',
            ])->where('total_wager_value', $expected['total_wager_value'])
                ->where('odds', $expected['odds'])
                ->where('selling_percentage', $expected['selling_percentage'])
                ->where('selling_price', $expected['selling_price'])
                ->where('current_selling_price', $expected['current_selling_price'])
                ->where('percentage_sold', $expected['percentage_sold'])
                ->where('amount_sold', $expected['amount_sold'])
        );
    }

    public static function validPayloadProvider(): array
    {
        return [
            'basic valid wager 1' => [
                [
                    'total_wager_value' => 100,
                    'odds' => 150,
                    'selling_percentage' => 50,
                    'selling_price' => 75.02,
                ],
                'expected' => [
                    'total_wager_value' => 100,
                    'odds' => 150,
                    'selling_percentage' => 50,
                    'selling_price' => 75.02,
                    'current_selling_price' => 75.02,
                    'percentage_sold' => null,
                    'amount_sold' => null,
                ],
            ],
            'basic valid wager 2' => [
                [
                    'total_wager_value' => 10,
                    'odds' => 10,
                    'selling_percentage' => 80,
                    'selling_price' => 81,
                ],
                'expected' => [
                    'total_wager_value' => 10,
                    'odds' => 10,
                    'selling_percentage' => 80,
                    'selling_price' => 81,
                    'current_selling_price' => 81,
                    'percentage_sold' => null,
                    'amount_sold' => null,
                ],
            ],
        ];
    }

    #[DataProvider('invalidPayloadProvider')]
    public function testPlaceWagerFailsWithInvalidPayload(
        array $payload,
        string $expectedError
    ): void {
        $response = $this->postJson('/api/wagers', $payload);
        $response->assertBadRequest();
        $response->assertJsonStructure(['error']);
        $this->assertSame($expectedError, $response->json('error'));
    }

    public static function invalidPayloadProvider(): array
    {
        return [
            'selling price too low' => [
                [
                    'total_wager_value' => 100,
                    'odds' => 150,
                    'selling_percentage' => 50,
                    'selling_price' => 49.2,
                ],
                'The selling price must be greater than 50.',
            ],
            'missing required fields' => [
                [],
                'The total wager value field is required. (and 3 more errors)',
            ],
            'invalid types' => [
                [
                    'total_wager_value' => 'abc',
                    'odds' => 'xyz',
                    'selling_percentage' => 'fifty',
                    'selling_price' => 'seventy',
                ],
                'The total wager value must be an integer. (and 3 more errors)',
            ],
            'out of range values' => [
                [
                    'total_wager_value' => 0,
                    'odds' => 0,
                    'selling_percentage' => 101,
                    'selling_price' => 0.00,
                ],
                'The total wager value must be at least 1. (and 3 more errors)',
            ],
            'total wager value is not an integer' => [
                [
                    'total_wager_value' => 1.1,
                    'odds' => 10,
                    'selling_percentage' => 80,
                    'selling_price' => 81,
                ],
                'The total wager value must be an integer.',
            ],
            'odds must be an integer' => [
                [
                    'total_wager_value' => 10,
                    'odds' => 1.1,
                    'selling_percentage' => 80,
                    'selling_price' => 81,
                ],
                'The odds must be an integer.',
            ],
            'odds must be greater than 0' => [
                [
                    'total_wager_value' => 10,
                    'odds' => -1,
                    'selling_percentage' => 80,
                    'selling_price' => 81,
                ],
                'The odds must be at least 1.',
            ],
            'selling percentage must be an integer' => [
                [
                    'total_wager_value' => 10,
                    'odds' => 9,
                    'selling_percentage' => 1.1,
                    'selling_price' => 81,
                ],
                'The selling percentage must be an integer.',
            ],
            'selling percentage must be greater than 0' => [
                [
                    'total_wager_value' => 10,
                    'odds' => 9,
                    'selling_percentage' => 0,
                    'selling_price' => 81,
                ],
                'The selling percentage must be between 1 and 100.',
            ],
            'selling percentage must not be greater than 100' => [
                [
                    'total_wager_value' => 10,
                    'odds' => 9,
                    'selling_percentage' => 101,
                    'selling_price' => 81,
                ],
                'The selling percentage must be between 1 and 100.',
            ],
        ];
    }
}
