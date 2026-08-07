<?php

namespace App\Domains\Shared\Actions;

use App\Domains\Shared\Contracts\ActionInterface;

/**
 * Base class for single-purpose application actions.
 * Controllers should call Actions/Services — not contain business rules.
 */
abstract class Action implements ActionInterface
{
    abstract public function execute(mixed ...$args): mixed;

    public function __invoke(mixed ...$args): mixed
    {
        return $this->execute(...$args);
    }
}
