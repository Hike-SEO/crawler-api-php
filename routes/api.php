<?php

use App\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('websites')->group(function () {
    Route::post('/', Api\Websites\CreateController::class)->name('api.websites.create');
});

Route::post('/crawl/single', Api\SingleCrawlController::class)->name('api.crawl.single');
Route::post('/crawl/full', Api\FullCrawlController::class)->name('api.crawl.full');
