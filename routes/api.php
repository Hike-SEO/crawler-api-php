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
    Route::get('/', Api\Websites\IndexController::class)->name('api.websites.index');
    Route::post('/', Api\Websites\CreateController::class)->name('api.websites.create');
    Route::put('/{website}', Api\Websites\UpdateController::class)->name('api.websites.update');
    Route::delete('/{website}', Api\Websites\DeleteController::class)->name('api.websites.delete');
});

Route::post('/crawl/single', Api\SingleCrawlController::class)->name('api.crawl.single');

Route::post('/capture/pdf', Api\PdfController::class)->name('api.capture.pdf');

Route::post('/capture/screenshot', Api\ScreenshotController::class)->name('api.capture.screenshot');
