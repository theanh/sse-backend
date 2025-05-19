<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Repositories\WagerRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class ListWagersApiTest extends TestCase
{
    use RefreshDatabase;

    protected WagerRepository $wagerRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->wagerRepository = $this->app->make(WagerRepository::class);
    }

    private function seedWagers(array $seed): void
    {
        foreach ($seed as $data) {
            $this->wagerRepository->create($data);
        }
    }

    #[DataProvider('emptyListProvider')]
    public function testListWagersEmpty(array $query): void
    {
        $response = $this->getJson('/api/wagers?' . http_build_query($query));
        $response->assertOk();
        $this->assertSame([], $response->json());
    }

    public static function emptyListProvider(): array
    {
        return [
            'empty list' => [
                ['page' => 1, 'limit' => 10],
            ],
        ];
    }

    #[DataProvider('nonEmptyListProvider')]
    public function testListWagersNonEmpty(array $seed, array $query, int $expectedCount): void
    {
        $this->seedWagers($seed);
        $response = $this->getJson('/api/wagers?' . http_build_query($query));
        $response->assertOk();
        $response->assertJson(fn (AssertableJson $json) =>
            $json->each(fn (AssertableJson $item) =>
                $item->hasAll([
                    'id',
                    'total_wager_value',
                    'odds',
                    'selling_percentage',
                    'selling_price',
                    'current_selling_price',
                    'percentage_sold',
                    'amount_sold',
                    'placed_at',
                ])
            )
        );
        $this->assertCount($expectedCount, $response->json());
    }

    public static function nonEmptyListProvider(): array
    {
        return [
            'single wager' => [
                [[
                    'total_wager_value' => 100,
                    'odds' => 150,
                    'selling_percentage' => 50,
                    'selling_price' => 75.00,
                ]],
                ['page' => 1, 'limit' => 10],
                1,
            ],
            'pagination' => [
                array_fill(0, 15, [
                    'total_wager_value' => 100,
                    'odds' => 150,
                    'selling_percentage' => 50,
                    'selling_price' => 75.00,
                ]),
                ['page' => 2, 'limit' => 10],
                5,
            ],
        ];
    }

    #[DataProvider('invalidQueryProvider')]
    public function testListWagersInvalidQuery(array $query, string $expectedError): void
    {
        $response = $this->getJson('/api/wagers?' . http_build_query($query));
        $response->assertBadRequest();
        $response->assertJsonStructure(['error']);
        $this->assertSame($expectedError, $response->json('error'));
    }

    public static function invalidQueryProvider(): array
    {
        return [
            'negative page' => [
                ['page' => -1, 'limit' => 10],
                'The page must be at least 1.',
            ],
            'zero page' => [
                ['page' => 0, 'limit' => 10],
                'The page must be at least 1.',
            ],
            'non-integer page' => [
                ['page' => 'abc', 'limit' => 10],
                'The page must be an integer.',
            ],
            'negative limit' => [
                ['page' => 1, 'limit' => -5],
                'The limit must be at least 1.',
            ],
            'zero limit' => [
                ['page' => 1, 'limit' => 0],
                'The limit must be at least 1.',
            ],
            'limit above max' => [
                ['page' => 1, 'limit' => 100],
                'The limit field must not be greater than 20.',
            ],
            'non-integer limit' => [
                ['page' => 1, 'limit' => 'abc'],
                'The limit must be an integer.',
            ],
        ];
    }

    #[DataProvider('paginationEdgeProvider')]
    public function testListWagersPaginationEdge(array $seed, array $query, int $expectedCount): void
    {
        $this->seedWagers($seed);
        $response = $this->getJson('/api/wagers?' . http_build_query($query));
        $response->assertOk();
        $this->assertCount($expectedCount, $response->json());
    }

    public static function paginationEdgeProvider(): array
    {
        return [
            'very large page returns empty' => [
                array_fill(0, 10, [
                    'total_wager_value' => 100,
                    'odds' => 150,
                    'selling_percentage' => 50,
                    'selling_price' => 75.00,
                ]),
                ['page' => 999, 'limit' => 10],
                0,
            ],
            'limit 1 returns one' => [
                array_fill(0, 5, [
                    'total_wager_value' => 100,
                    'odds' => 150,
                    'selling_percentage' => 50,
                    'selling_price' => 75.00,
                ]),
                ['page' => 1, 'limit' => 1],
                1,
            ],
            'limit max returns max' => [
                array_fill(0, 25, [
                    'total_wager_value' => 100,
                    'odds' => 150,
                    'selling_percentage' => 50,
                    'selling_price' => 75.00,
                ]),
                ['page' => 1, 'limit' => 20],
                20,
            ],
            'default values' => [
                array_fill(0, 3, [
                    'total_wager_value' => 100,
                    'odds' => 150,
                    'selling_percentage' => 50,
                    'selling_price' => 75.00,
                ]),
                [],
                3,
            ],
        ];
    }
}
