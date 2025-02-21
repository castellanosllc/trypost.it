<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// social accounts
use App\Http\Controllers\Account\HomeController;
use App\Http\Controllers\Account\LinkedinController;
use App\Http\Controllers\Account\LinkedinPageController;
use App\Http\Controllers\Account\TwitterController;
use App\Http\Controllers\Account\TikTokController;

Route::group(
    [
        'middleware' => [
            'auth',
            'verified',
            'billing'
        ],
    ],
    function () {

        // accounts
        Route::get('/accounts', [HomeController::class, 'index'])->name('accounts.index');

        // linkedin
        Route::get('/accounts/linkedin/connect', [LinkedinController::class, 'connect'])->name('accounts.linkedin.connect');
        Route::get('/accounts/linkedin/callback', [LinkedinController::class, 'callback'])->name('accounts.linkedin.callback');

        // linkedin page
        Route::get('/accounts/linkedin-page/connect', [LinkedinPageController::class, 'connect'])->name('accounts.linkedin-page.connect');
        Route::get('/accounts/linkedin-page/callback', [LinkedinPageController::class, 'callback'])->name('accounts.linkedin-page.callback');
        Route::get('/accounts/linkedin-page/select', [LinkedinPageController::class, 'selectPage'])->name('accounts.linkedin-page.select');
        Route::post('/accounts/linkedin-page/store', [LinkedinPageController::class, 'store'])->name('accounts.linkedin-page.store');

        // twitter
        Route::get('/accounts/twitter/connect', [TwitterController::class, 'connect'])->name('accounts.twitter.connect');
        Route::get('/accounts/twitter/callback', [TwitterController::class, 'callback'])->name('accounts.twitter.callback');

        // tiktok
        Route::get('/accounts/tiktok/connect', [TikTokController::class, 'connect'])->name('accounts.tiktok.connect');
        Route::get('/accounts/tiktok/callback', [TikTokController::class, 'callback'])->name('accounts.tiktok.callback');
    }
);
