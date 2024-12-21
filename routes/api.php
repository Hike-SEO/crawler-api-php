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
Route::prefix('/crawl')
    ->name('api.crawl.')
    ->group(function () {
        Route::post('single', Api\SingleCrawlController::class)
            ->name('single');

        Route::post('robots', Api\RobotsController::class)
            ->name('robots');
    });

Route::prefix('websites')
    ->name('api.websites.')
    ->group(function () {
        Route::get('/', Api\Websites\IndexController::class)->name('index');
        Route::post('/', Api\Websites\CreateController::class)->name('create');
        Route::put('/{website}', Api\Websites\UpdateController::class)->name('update');
        Route::delete('/{website}', Api\Websites\DeleteController::class)->name('delete');
    });

Route::prefix('/capture')
    ->name('api.capture.')
    ->group(function () {
        Route::post('/pdf', Api\PdfController::class)->name('pdf');
        Route::post('/screenshot', Api\ScreenshotController::class)->name('screenshot');

    });
