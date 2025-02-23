<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// general
use App\Http\Controllers\SpaceController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostContentController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\HashtagController;
use App\Http\Controllers\MediaLibraryController;

Route::group(
    [
        'middleware' => [
            'auth',
            'verified',
            'billing'
        ],
    ],
    function () {

        // spaces
        Route::get('/spaces/create', [SpaceController::class, 'create'])->name('spaces.create');
        Route::post('/spaces', [SpaceController::class, 'store'])->name('spaces.store');
        Route::put('/spaces/update-current', [SpaceController::class, 'setCurrentStore'])->name('spaces.update-current');

        // posts
        Route::get('/posts/{id?}', [PostController::class, 'index'])->name('posts.index');
        Route::post('/posts/clone/{id}', [PostController::class, 'clone'])->name('posts.clone');
        Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
        Route::put('/posts/{id}', [PostController::class, 'update'])->name('posts.update');
        Route::delete('/posts/{id}', [PostController::class, 'destroy'])->name('posts.destroy');

        // post contents
        Route::post('/posts/{id}/post-contents', [PostContentController::class, 'store'])->name('post-contents.store');
        Route::put('/posts/{id}/post-contents/{postContentId}', [PostContentController::class, 'update'])->name('post-contents.update');
        Route::delete('/posts/{id}/post-contents/{postContentId}', [PostContentController::class, 'destroy'])->name('post-contents.destroy');

        // media library
        Route::get('/media-library', [MediaLibraryController::class, 'index'])->name('media-library.index');

        // medias
        Route::get('/medias/{id}/download', [MediaController::class, 'download'])->name('medias.download')->withoutMiddleware('*');
        Route::post('/medias/upload', [MediaController::class, 'store'])->name('medias.store');
        Route::post('/medias/copy', [MediaController::class, 'copy'])->name('medias.copy');
        Route::delete('/medias/{modelId}/{id}', [MediaController::class, 'destroy'])->name('medias.destroy');

        // tags
        Route::get('/tags', [TagController::class, 'index'])->name('setting.tags.index');
        Route::post('/tags', [TagController::class, 'store'])->name('setting.tags.store');
        Route::post('/tags/sort', [TagController::class, 'sort'])->name('setting.tags.sort');
        Route::put('/tags/{id}', [TagController::class, 'update'])->name('setting.tags.update');
        Route::delete('/tags/{id}', [TagController::class, 'destroy'])->name('setting.tags.destroy');

        // hashtags
        Route::get('/hashtags', [HashtagController::class, 'index'])->name('setting.hashtags.index');
        Route::post('/hashtags', [HashtagController::class, 'store'])->name('setting.hashtags.store');
        Route::put('/hashtags/{id}', [HashtagController::class, 'update'])->name('setting.hashtags.update');
        Route::delete('/hashtags/{id}', [HashtagController::class, 'destroy'])->name('setting.hashtags.destroy');
    }
);
