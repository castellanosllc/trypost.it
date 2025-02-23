<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => function () use ($request) {
                    if (! $request->user()) {
                        return;
                    }

                    return array_merge($request->user()->toArray(), array_filter([
                        'workspace' => $request->user()->workspace,
                        'current_space' => $request->user()->currentSpace,
                        'spaces' => $request->user()->workspace->spaces,
                    ]));
                },
            ],
            'csrf_token' => fn () => csrf_token(),
            'flash' => $request->session()->get('flash', []),
            'env' => config('app.env'),
            'locale' => app()->getLocale(),
        ];
    }
}
