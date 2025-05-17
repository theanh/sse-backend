<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CreateWagerRequest;
use App\Repositories\PurchaseRepository;
use App\Repositories\WagerRepository;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\WagerResource;

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
}
