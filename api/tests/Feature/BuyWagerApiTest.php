<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Repositories\WagerRepository;
use App\Repositories\PurchaseRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class BuyWagerApiTest extends TestCase
{
    use RefreshDatabase;

    protected WagerRepository $wagerRepository;
    protected PurchaseRepository $purchaseRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->wagerRepository = $this->app->make(WagerRepository::class);
        $this->purchaseRepository = $this->app->make(PurchaseRepository::class);
    }

    #[DataProvider('validBuyPayloadProvider')]
    public function testBuyWagerSuccess(array $wagerData, array $payload, array $expected): void
    {
        $wager = $this->wagerRepository->create($wagerData);

        $response = $this->postJson("/api/buy/{$wager->id}", $payload);

        $response->assertCreated();
        $this->assertSame(201, $response->status());
        $response->assertJson(fn (AssertableJson $json) =>
            $json->hasAll(['id', 'wager_id', 'buying_price', 'bought_at'])
                ->where('wager_id', $wager->id)
                ->where('buying_price', $expected['buying_price'])
        );
    }

    public static function validBuyPayloadProvider(): array
    {
        return [
            'basic buy' => [
                [
                    'total_wager_value' => 100,
                    'odds' => 150,
                    'selling_percentage' => 50,
                    'selling_price' => 75,
                    'current_selling_price' => 75,
                ],
                ['buying_price' => 50],
                ['buying_price' => 50],
            ],
            'buy exact remaining' => [
                [
                    'total_wager_value' => 100,
                    'odds' => 150,
                    'selling_percentage' => 50,
                    'selling_price' => 75.00,
                    'current_selling_price' => 75,
                ],
                ['buying_price' => 75],
                ['buying_price' => 75],
            ],
        ];
    }

    public function testSequentialBuysUpdateWagerState(): void
    {
        $wagerData = [
            'total_wager_value' => 100,
            'odds' => 150,
            'selling_percentage' => 50,
            'selling_price' => 75.00,
            'current_selling_price' => 75.00,
        ];
        $wager = $this->wagerRepository->create($wagerData);

        $this->postJson("/api/buy/{$wager->id}", ['buying_price' => 30])->assertCreated();
        $wager->refresh();
        $this->assertEquals(45, $wager->current_selling_price);
        $this->assertEquals(30, $wager->amount_sold);
        $this->assertEquals(40, $wager->percentage_sold);

        $this->postJson("/api/buy/{$wager->id}", ['buying_price' => 45])->assertCreated();
        $wager->refresh();
        $this->assertEquals(0, $wager->current_selling_price);
        $this->assertEquals(75, $wager->amount_sold);
        $this->assertEquals(100, $wager->percentage_sold);

        $this->postJson("/api/buy/{$wager->id}", ['buying_price' => 10])->assertBadRequest();
    }

    #[DataProvider('invalidBuyPayloadProvider')]
    public function testBuyWagerFailsWithInvalidPayload(
        ?array $wagerData,
        array $payload,
        string $expectedError
    ): void {
        $wager = $wagerData ? $this->wagerRepository->create($wagerData) : null;
        $wagerId = $wager ? $wager->id : 999999;
        $response = $this->postJson("/api/buy/{$wagerId}", $payload);
        $response->assertBadRequest();
        $response->assertJsonStructure(['error']);
        $this->assertSame($expectedError, $response->json('error'));
    }

    public static function invalidBuyPayloadProvider(): array
    {
        return [
            'missing buying_price' => [
                [
                    'total_wager_value' => 100,
                    'odds' => 150,
                    'selling_percentage' => 50,
                    'selling_price' => 75.00,
                    'current_selling_price' => 75.00,
                ],
                [],
                'The buying price field is required.',
            ],
            'buying_price not numeric' => [
                [
                    'total_wager_value' => 100,
                    'odds' => 150,
                    'selling_percentage' => 50,
                    'selling_price' => 75.00,
                    'current_selling_price' => 75.00,
                ],
                ['buying_price' => 'abc'],
                'The buying price field must be a number.',
            ],
            'buying_price too high' => [
                [
                    'total_wager_value' => 100,
                    'odds' => 150,
                    'selling_percentage' => 50,
                    'selling_price' => 75.00,
                    'current_selling_price' => 75.00,
                ],
                ['buying_price' => 100.00],
                'The buying price must be less than or equal to 75.00.',
            ],
            'buying_price zero' => [
                [
                    'total_wager_value' => 100,
                    'odds' => 150,
                    'selling_percentage' => 50,
                    'selling_price' => 75.00,
                    'current_selling_price' => 75.00,
                ],
                ['buying_price' => 0],
                'The buying price must be at least 0.01.',
            ],
            'wager not found' => [
                null,
                ['buying_price' => 10],
                'Not found',
            ],
            'negative buying_price' => [
                [
                    'total_wager_value' => 100,
                    'odds' => 150,
                    'selling_percentage' => 50,
                    'selling_price' => 75.00,
                    'current_selling_price' => 75.00,
                ],
                ['buying_price' => -10],
                'The buying price must be at least 0.01.',
            ],
        ];
    }
}
