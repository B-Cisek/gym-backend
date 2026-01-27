<?php

declare(strict_types=1);

namespace App\Shared\Domain;

abstract class DomainException extends \LogicException
{
    abstract public function getHttpStatusCode(): int;
}
