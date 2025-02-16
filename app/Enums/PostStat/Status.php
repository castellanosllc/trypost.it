<?php

namespace App\Enums\PostStat;

enum Status: string
{
    case PUBLISHED = 'published';
    case FAILED = 'failed';
}
