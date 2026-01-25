<?php

declare(strict_types=1);

namespace App\Auth\Domain;

enum UserRole: string
{
    case USER = 'ROLE_USER'; # default role for all users
    case OWNER = 'ROLE_OWNER';
    case STAFF = 'ROLE_STAFF';
    case MEMBER = 'ROLE_MEMBER';
}
