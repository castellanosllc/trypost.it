<?php

namespace App\Providers;

use Laravel\Cashier\Cashier;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

use App\Policies\WorkspacePolicy;
use App\Policies\AccountPolicy;

use App\Models\Workspace;
use App\Models\User;
use App\Models\Account;
use App\Models\Invite;
use App\Models\Post;
use App\Models\PostContent;
use App\Models\Media;
use App\Models\Language;
use App\Models\Plan;
use App\Models\Space;
use App\Models\Tag;
use App\Models\Hashtag;

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
        // Socialite providers
        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('tiktok', \SocialiteProviders\TikTok\Provider::class);
            $event->extendSocialite('threads', \SocialiteProviders\Threads\Provider::class);
            $event->extendSocialite('youtube', \SocialiteProviders\YouTube\Provider::class);
            $event->extendSocialite('pinterest', \SocialiteProviders\Pinterest\Provider::class);
            $event->extendSocialite('facebook', \SocialiteProviders\Facebook\Provider::class);
        });

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
        Gate::policy(Account::class, AccountPolicy::class);

        // Morph map for polymorphic relationships
        Relation::enforceMorphMap([
            'account' => Account::class,
            'invite' => Invite::class,
            'language' => Language::class,
            'media' => Media::class,
            'plan' => Plan::class,
            'post' => Post::class,
            'postContent' => PostContent::class,
            'user' => User::class,
            'workspace' => Workspace::class,
            'space' => Space::class,
            'tag' => Tag::class,
            'hashtag' => Hashtag::class,
        ]);
    }
}
