<?php

declare(strict_types=1);

namespace App\Shared\Application\Service;

interface Mailer
{
    public function send(Email $email): void;
}
