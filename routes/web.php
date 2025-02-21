<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\SocialAccount\HomeController as SocialAccountHomeController;
use App\Http\Controllers\SocialAccount\LinkedinController;
use App\Http\Controllers\SocialAccount\LinkedinPageController;
use App\Http\Controllers\SocialAccount\TwitterController;
use App\Http\Controllers\SocialAccount\TiktokController;

use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\HashtagController;


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

        // accounts
        Route::get('/social-accounts', [SocialAccountHomeController::class, 'index'])->name('social-accounts.index');

        // linkedin
        Route::get('/social-accounts/linkedin/connect', [LinkedinController::class, 'connect'])->name('social-accounts.linkedin.connect');
        Route::get('/social-accounts/linkedin/callback', [LinkedinController::class, 'callback'])->name('social-accounts.linkedin.callback');

        // linkedin page
        Route::get('/social-accounts/linkedin-page/connect', [LinkedinPageController::class, 'connect'])->name('social-accounts.linkedin-page.connect');
        Route::get('/social-accounts/linkedin-page/callback', [LinkedinPageController::class, 'callback'])->name('social-accounts.linkedin-page.callback');
        Route::get('/social-accounts/linkedin-page/select', [LinkedinPageController::class, 'selectPage'])->name('social-accounts.linkedin-page.select');
        Route::post('/social-accounts/linkedin-page/store', [LinkedinPageController::class, 'store'])->name('social-accounts.linkedin-page.store');

        // twitter
        Route::get('/social-accounts/twitter/connect', [TwitterController::class, 'connect'])->name('social-accounts.twitter.connect');
        Route::get('/social-accounts/twitter/callback', [TwitterController::class, 'callback'])->name('social-accounts.twitter.callback');

        // tiktok
        Route::get('/social-accounts/tiktok/connect', [TiktokController::class, 'connect'])->name('social-accounts.tiktok.connect');
        Route::get('/social-accounts/tiktok/callback', [TiktokController::class, 'callback'])->name('social-accounts.tiktok.callback');

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
