<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Workspace;

class WorkspacePolicy
{
    /**
     * Determine if the workspace has reached the accounts limit.
     */
    public function reachedAccountsLimit(?User $user, Workspace $workspace): bool
    {
        return !$workspace->usage()['accounts']['reached_limit'];
    }
}
