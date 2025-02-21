<?php

namespace App\Enums\SocialAccount;

enum Status: string
{
    case CONNECTED = 'connected';
    case FAILED = 'failed';
}
