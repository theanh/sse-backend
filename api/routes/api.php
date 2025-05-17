<?php

declare(strict_types=1);

use App\Http\Controllers\WagerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api'])->group(function () {
    Route::post('/wagers', [WagerController::class, 'create']);
});
