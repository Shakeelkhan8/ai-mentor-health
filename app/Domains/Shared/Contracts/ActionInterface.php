<?php

namespace App\Domains\Shared\Contracts;

interface ActionInterface
{
    /**
     * Execute a single application use-case.
     *
     * @param  mixed  ...$args
     */
    public function execute(mixed ...$args): mixed;
}
