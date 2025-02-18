<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class Billing
{
    public function handle(Request $request, Closure $next)
    {
        $workspace = $request->user()?->currentWorkspace;

        $subscription = $workspace->subscribed('default');
        $onTrial = $workspace->subscription('default')->onTrial();
        $isOnBillingPage = $request->routeIs('setting.billing.*');

        if (!$isOnBillingPage && (!$subscription || !$onTrial)) {
            return redirect(route('setting.billing.start-trial'));
        }

        Inertia::share('usage', $request->user()->currentWorkspace->usage());
        return $next($request);
    }
}
