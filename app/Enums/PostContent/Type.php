<?php

namespace App\Enums\PostContent;

enum Type: string
{
    case TEXT = 'text';
    case IMAGE = 'image';
    case VIDEO = 'video';

    case REEL = 'reel';
    case CAROUSEL = 'carousel';
    case STORY = 'story';
}
