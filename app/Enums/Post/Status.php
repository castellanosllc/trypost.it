<?php

namespace App\Enums\Post;

enum Status: string
{
    case GHOST = 'ghost';
    case DRAFT = 'draft';
    case SCHEDULED = 'scheduled';
    case PUBLISHED = 'published';
    case FAILED = 'failed';
}
