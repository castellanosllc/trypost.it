<?php

declare(strict_types=1);

namespace App\Models\Traits;


use App\Models\Plan;

use Carbon\Carbon;

trait WorkspaceUsage
{
    public function usage()
    {
        return [
            'billing' => [
                'has_subscription' => $this->subscribed('default'),
                'past_due' => $this->hasIncompletePayment('default') ?? null,
                'canceled' => $this->subscription('default') ? $this->subscription('default')->canceled() : false,
                'active' => $this->subscribed('default'),
            ],
            'plan' => [
                'name' => $this->plan->name,
                'access_level' => $this->plan->access_level,
                'can_create_teams' => $this->plan->can_create_teams,
                'next_tier' => Plan::where('access_level', '>', $this->plan->access_level)
                    ->orderBy('access_level', 'asc')
                    ->select('name')
                    ->first(),
            ],
            'accounts' => [
                'used' => number_format($this->accounts->count()),
                'limit' => number_format($this->plan->max_accounts),
                'percent' => $this->accounts->count() === 0 ? 0 : round(($this->accounts->count() / $this->plan->max_accounts) * 100),
                'remaining' => number_format($this->plan->max_accounts - $this->accounts->count()),
                'reached_limit' => $this->accounts->count() >= $this->plan->max_accounts,
            ],
        ];
    }
}
