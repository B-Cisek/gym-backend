<?php

declare(strict_types=1);

namespace App\Shared\Domain;

interface EventDispatcher
{
    public function dispatch(object $event): void;
}
