<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\MediaController;

// setting
use App\Http\Controllers\Setting\AccountController;
use App\Http\Controllers\Setting\WorkspaceController as SettingWorkspaceController;
use App\Http\Controllers\Setting\UsageController;
use App\Http\Controllers\Setting\BillingController;
use App\Http\Controllers\Setting\InviteController;
use App\Http\Controllers\Setting\TeamMemberController;

Route::group(
    [
        'middleware' => [
            'auth',
            'verified',
            'set-workspace',
            'billing'
        ],
    ],
    function () {

        // workspaces
        Route::get('/workspaces/create', [WorkspaceController::class, 'create'])->name('workspaces.create')->withoutMiddleware(['billing']);
        Route::post('/workspaces', [WorkspaceController::class, 'store'])->name('workspaces.store')->withoutMiddleware(['billing']);
        Route::put('/workspaces/update-current', [WorkspaceController::class, 'setCurrentStore'])->name('workspaces.update-current')->withoutMiddleware(['billing']);

        // calendar
        Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');

        // posts
        Route::get('/posts/{id?}', [PostController::class, 'index'])->name('posts.index');
        Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
        Route::put('/posts/{id}', [PostController::class, 'update'])->name('posts.update');
        Route::delete('/posts/{id}', [PostController::class, 'destroy'])->name('posts.destroy');

        // medias
        Route::get('/medias/{id}/download', [MediaController::class, 'download'])->name('medias.download')->withoutMiddleware('*');
        Route::post('/medias', [MediaController::class, 'store'])->name('medias.store');
        Route::post('/medias/sort', [MediaController::class, 'sort'])->name('medias.sort');
        Route::post('/medias/{modelId}/thumbnail/{id}', [MediaController::class, 'thumbnail'])->name('medias.thumbmail');
        Route::delete('/medias/{modelId}/{id}', [MediaController::class, 'destroy'])->name('medias.destroy');

        // settings
        Route::prefix('settings')->group(function () {

            // account
            Route::get('/account', [AccountController::class, 'edit'])->name('setting.account.edit')->withoutMiddleware(['set-store']);
            Route::post('/account', [AccountController::class, 'update'])->name('setting.account.update')->withoutMiddleware(['set-store']);
            Route::delete('/account/photo', [AccountController::class, 'deletePhoto'])->name('setting.account.photo.destroy')->withoutMiddleware(['set-store']);

            // workspace
            Route::get('/workspace', [SettingWorkspaceController::class, 'edit'])->name('setting.workspace.edit');
            Route::put('/workspace', [SettingWorkspaceController::class, 'update'])->name('setting.workspace.update');
            Route::delete('/workspace/photo', [SettingWorkspaceController::class, 'deleteLogo'])->name('setting.workspace.logo.destroy');

            // billing
            Route::get('/billing', [BillingController::class, 'index'])->name('setting.billing.index');
            Route::get('/billing/upgrade', [BillingController::class, 'upgrade'])->name('setting.billing.upgrade');
            Route::get('/billing/checkout/{planId}', [BillingController::class, 'checkout'])->name('setting.billing.checkout');
            Route::get('/billing/portal', [BillingController::class, 'billingPortal'])->name('setting.billing.portal');
            Route::get('/billing/swap-free-plan', [BillingController::class, 'swapFreePlan'])->name('setting.billing.swap-free-plan');
            Route::inertia('/billing/checkout-success', 'Setting/Billing/Success')->name('setting.billing.checkout-success');

            // users
            Route::get('/users', [TeamMemberController::class, 'index'])->name('setting.team-members.index');
            Route::put('/users/{id}/role', [TeamMemberController::class, 'updateUserRole'])->name('setting.team-members.role');
            Route::delete('/users/leave', [TeamMemberController::class, 'leave'])->name('setting.team-members.leave');
            Route::delete('/users/{id}/remove-from-team', [TeamMemberController::class, 'destroy'])->name('setting.team-members.destroy');

            // user invites
            Route::get('/users/invites/create', [InviteController::class, 'create'])->name('setting.invites.create');
            Route::post('/users/invites', [InviteController::class, 'store'])->name('setting.invites.store');
            Route::delete('/users/invites/{id}', [InviteController::class, 'destroy'])->name('setting.invites.destroy');

            // usage
            Route::get('/usage', [UsageController::class, 'index'])->name('setting.usage.index');
        });
    }
);

require __DIR__ . '/auth.php';
