<?php

use App\Http\Controllers\Api\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/generate2', function (Request $request) {
    Log::debug('Request received');
  
    return response()->json('test', 200, [
        'Access-Control-Allow-Origin' => '*',
        'X-Test' => 'test'
    ]);
});

Route::post('/generate', [ReportController::class, 'generate']);