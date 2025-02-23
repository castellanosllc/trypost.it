<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// social accounts
use App\Http\Controllers\Account\HomeController;
use App\Http\Controllers\Account\LinkedinController;
use App\Http\Controllers\Account\LinkedinPageController;
use App\Http\Controllers\Account\TwitterController;
use App\Http\Controllers\Account\TikTokController;
use App\Http\Controllers\Account\ThreadsController;
use App\Http\Controllers\Account\YoutubeController;
use App\Http\Controllers\Account\PinterestController;
use App\Http\Controllers\Account\FacebookController;

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

        // threads
        Route::get('/accounts/threads/connect', [ThreadsController::class, 'connect'])->name('accounts.threads.connect');
        Route::get('/accounts/threads/callback', [ThreadsController::class, 'callback'])->name('accounts.threads.callback');

        // youtube
        Route::get('/accounts/youtube/connect', [YoutubeController::class, 'connect'])->name('accounts.youtube.connect');
        Route::get('/accounts/youtube/callback', [YoutubeController::class, 'callback'])->name('accounts.youtube.callback');

        // pinterest
        Route::get('/accounts/pinterest/connect', [PinterestController::class, 'connect'])->name('accounts.pinterest.connect');
        Route::get('/accounts/pinterest/callback', [PinterestController::class, 'callback'])->name('accounts.pinterest.callback');

        // facebook
        Route::get('/accounts/facebook/connect', [FacebookController::class, 'connect'])->name('accounts.facebook.connect');
        Route::get('/accounts/facebook/callback', [FacebookController::class, 'callback'])->name('accounts.facebook.callback');
    }
);
