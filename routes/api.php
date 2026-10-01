<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeoController;
use App\Http\Controllers\UserTransactionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/transactions', [UserTransactionController::class, 'fetchTransactions']);

// ── Geo (public, cached server-side) ──────────────────────────────────────────
Route::get('/geo/countries',             [GeoController::class, 'countries']);
Route::get('/geo/states',                [GeoController::class, 'states']);
// Response is cached server-side forever, so a generous throttle is plenty —
// a tight limit (e.g. 60/min) trips during normal use and makes the states
// dropdown appear to vanish when the request is rejected with a 429.
Route::get('/countries/{country}/states',[GeoController::class, 'statesForCountry'])->middleware('throttle:600,1');