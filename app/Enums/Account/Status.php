<?php

namespace App\Enums\Account;

enum Status: string
{
    case CONNECTED = 'connected';
    case FAILED = 'failed';
}
