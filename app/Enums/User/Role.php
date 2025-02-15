<?php

namespace App\Enums\User;

enum Role: string
{
    case OWNER = 'owner';
    case ADMIN = 'admin';
    case OPERATOR = 'operator';
}
