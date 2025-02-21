<?php

namespace App\Providers;

use Laravel\Cashier\Cashier;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

use App\Policies\WorkspacePolicy;
use App\Policies\SocialAccountPolicy;

use App\Models\Workspace;
use App\Models\User;
use App\Models\SocialAccount;
use App\Models\Invite;
use App\Models\Post;
use App\Models\PostStat;
use App\Models\Media;
use App\Models\Language;
use App\Models\Plan;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Cashier configuration
        Cashier::useCustomerModel(Workspace::class);

        // Prefetch assets
        Vite::prefetch(concurrency: 3);

        // Custom email verification template
        VerifyEmail::toMailUsing(function (User $user, string $url) {
            return (new MailMessage)
                ->subject('Verify Email Address')
                ->view('mail.email-verification', [
                    'title' => 'Confirm your email address',
                    'previewText' => 'Please confirm your email address.',
                    'user' => $user,
                    'url' => $url
                ]);
        });

        // Gate policies
        Gate::policy(Workspace::class, WorkspacePolicy::class);
        Gate::policy(SocialAccount::class, SocialAccountPolicy::class);

        // Morph map for polymorphic relationships
        Relation::enforceMorphMap([
            'socialAccount' => SocialAccount::class,
            'invite' => Invite::class,
            'language' => Language::class,
            'media' => Media::class,
            'plan' => Plan::class,
            'post' => Post::class,
            'postStat' => PostStat::class,
            'user' => User::class,
            'workspace' => Workspace::class,
        ]);
    }
}
