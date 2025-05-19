<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CreateWagerRequest;
use App\Repositories\PurchaseRepository;
use App\Repositories\WagerRepository;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\WagerResource;
use App\Http\Requests\BuyWagerRequest;
use App\Http\Resources\PurchaseResource;
use App\Http\Requests\ListWagersRequest;

class WagerController extends Controller
{
    public function __construct(
        private readonly WagerRepository $wagerRepository,
        private readonly PurchaseRepository $purchaseRepository,
    ) {
    }

    public function create(CreateWagerRequest $request): JsonResponse
    {
        try {
            $wager = $this->wagerRepository->create($request->validated());
            return (new WagerResource($wager))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => __('messages.internal_error'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function buy(BuyWagerRequest $request, int $wagerId): JsonResponse
    {
        try {
            $buyingPrice = (float) $request->input('buying_price');
            $purchase = $this->purchaseRepository->buy($wagerId, $buyingPrice);
            return (new PurchaseResource($purchase))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => __('messages.internal_error'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function list(ListWagersRequest $request): JsonResponse
    {
        $page = (int) $request->validated('page', 1);
        $limit = (int) $request->validated('limit', 10);
        $wagers = $this->wagerRepository->list($page, $limit);

        return response()->json(
            WagerResource::collection($wagers)->resolve(),
            Response::HTTP_OK
        );
    }
}
