<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function() {
    return redirect('/report');
});

Route::get('/report', [ReportController::class, 'index']);
Route::get('/report/new', [ReportController::class, 'new']);
Route::post('/report/add', [ReportController::class, 'add']);
Route::get('/report/{id}/rebuild', [ReportController::class, 'rebuild']);
Route::get('/report/{id}/pdf', [ReportController::class, 'downloadPdf']);
Route::get('/report/{id}/xml1', [ReportController::class, 'downloadXml1']);
Route::get('/report/{id}/xml2', [ReportController::class, 'downloadXml2']);