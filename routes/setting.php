<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Setting\AccountController;
use App\Http\Controllers\Setting\WorkspaceController;
use App\Http\Controllers\Setting\UsageController;
use App\Http\Controllers\Setting\BillingController;
use App\Http\Controllers\Setting\InviteController;
use App\Http\Controllers\Setting\TeamMemberController;

Route::group(
    [
        'middleware' => [
            'auth',
            'verified',
            'billing'
        ],
        'prefix' => 'settings'
    ],
    function () {
        // account
        Route::get('/account', [AccountController::class, 'edit'])->name('setting.account.edit');
        Route::post('/account', [AccountController::class, 'update'])->name('setting.account.update');
        Route::delete('/account/photo', [AccountController::class, 'deletePhoto'])->name('setting.account.photo.destroy');

        // workspace
        Route::get('/workspace', [WorkspaceController::class, 'edit'])->name('setting.workspace.edit');
        Route::put('/workspace', [WorkspaceController::class, 'update'])->name('setting.workspace.update');
        Route::delete('/workspace/photo', [WorkspaceController::class, 'deleteLogo'])->name('setting.workspace.logo.destroy');

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
    }
);
