<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Account\HomeController as AccountHomeController;
use App\Http\Controllers\Account\LinkedinController;
use App\Http\Controllers\Account\LinkedinPageController;
use App\Http\Controllers\Account\TwitterController;

use App\Http\Controllers\WorkspaceController;
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
        Route::get('/workspaces', [WorkspaceController::class, 'index'])->name('workspaces.index')->withoutMiddleware(['billing']);
        Route::get('/workspaces/create', [WorkspaceController::class, 'create'])->name('workspaces.create')->withoutMiddleware(['billing']);
        Route::post('/workspaces', [WorkspaceController::class, 'store'])->name('workspaces.store')->withoutMiddleware(['billing']);
        Route::put('/workspaces/update-current', [WorkspaceController::class, 'setCurrentStore'])->name('workspaces.update-current')->withoutMiddleware(['billing']);

        // posts
        Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
        Route::get('/posts/{id}', [PostController::class, 'edit'])->name('posts.edit');
        Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
        Route::put('/posts/{id}', [PostController::class, 'update'])->name('posts.update');
        Route::delete('/posts/{id}', [PostController::class, 'destroy'])->name('posts.destroy');

        // medias
        Route::get('/medias/{id}/download', [MediaController::class, 'download'])->name('medias.download')->withoutMiddleware('*');
        Route::post('/medias', [MediaController::class, 'store'])->name('medias.store');
        Route::post('/medias/sort', [MediaController::class, 'sort'])->name('medias.sort');
        Route::post('/medias/{modelId}/thumbnail/{id}', [MediaController::class, 'thumbnail'])->name('medias.thumbmail');
        Route::delete('/medias/{modelId}/{id}', [MediaController::class, 'destroy'])->name('medias.destroy');

        // accounts
        Route::get('/accounts', [AccountHomeController::class, 'index'])->name('accounts.index');

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

        // settings
        Route::prefix('settings')->group(function () {

            // account
            Route::get('/account', [AccountController::class, 'edit'])->name('setting.account.edit');
            Route::post('/account', [AccountController::class, 'update'])->name('setting.account.update');
            Route::delete('/account/photo', [AccountController::class, 'deletePhoto'])->name('setting.account.photo.destroy');

            // workspace
            Route::get('/workspace', [SettingWorkspaceController::class, 'edit'])->name('setting.workspace.edit');
            Route::put('/workspace', [SettingWorkspaceController::class, 'update'])->name('setting.workspace.update');
            Route::delete('/workspace/photo', [SettingWorkspaceController::class, 'deleteLogo'])->name('setting.workspace.logo.destroy');

            // billing
            Route::get('/billing/start-trial', [BillingController::class, 'trial'])->name('setting.billing.start-trial');
            Route::get('/billing', [BillingController::class, 'index'])->name('setting.billing.index');
            Route::get('/billing/checkout/{id}', [BillingController::class, 'checkout'])->name('setting.billing.checkout');
            Route::get('/billing/portal', [BillingController::class, 'billingPortal'])->name('setting.billing.portal');
            Route::get('/billing/success', [BillingController::class, 'checkoutSuccess'])->name('setting.billing.checkout-success');

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
