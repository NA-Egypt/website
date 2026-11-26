<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

class UserFacingException extends RuntimeException
{
    public readonly string $errorCode;

    public readonly int $statusCode;

    public function __construct(
        string $message = '',
        string $errorCode = 'domain_error',
        int $statusCode = 422,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
        $this->errorCode = $errorCode;
        $this->statusCode = $statusCode;
    }
}
