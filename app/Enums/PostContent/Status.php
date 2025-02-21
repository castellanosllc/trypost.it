<?php

namespace App\Enums\PostContent;

enum Status: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case FAILED = 'failed';
}
