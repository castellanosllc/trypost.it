<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\Link;
use App\Models\LinkStat;
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
                'next_tier' => Plan::where('access_level', '>', $this->plan->access_level)
                    ->orderBy('access_level', 'asc')
                    ->select('name')
                    ->first(),
            ],
        ];
    }
}
