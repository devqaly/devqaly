<?php

use App\Http\Controllers\Resources\EventController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get(
        'events/networkRequest/byRequestId/{networkRequestEvent:request_id}/databaseTransactions',
        [EventController::class, 'getDatabaseTransactionsByRequestId']
    )->name('eventNetworkRequest.byRequestId.databaseTransactions');

    Route::get('events/networkRequest/byRequestId/{networkRequestEvent:request_id}/logs', [EventController::class, 'getLogsByRequestId']);
});
