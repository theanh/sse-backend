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
            $wager = $this->wagerRepository->findOne($wagerId);
            $buyingPrice = (float) $request->input('buying_price');
            $purchase = $this->purchaseRepository->buy($wager, $buyingPrice);
            return (new PurchaseResource($purchase))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => __('messages.internal_error'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
