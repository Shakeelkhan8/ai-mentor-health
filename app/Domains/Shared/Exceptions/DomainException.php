<?php

namespace App\Domains\Shared\Exceptions;

use Exception;
use Throwable;

/**
 * Domain-level exception. Map to HTTP in the exception handler / controllers.
 */
class DomainException extends Exception
{
    public function __construct(
        string $message = '',
        public readonly string $errorCode = 'domain_error',
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
