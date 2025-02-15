<?php

namespace App\Enums;

enum CacheKey: string
{
    // case WORKSPACE_LAST_POST_ID = 'workspace-last-post-';

    public function key(string|int $suffix = ''): string
    {
        return $this->value . $suffix;
    }
}
