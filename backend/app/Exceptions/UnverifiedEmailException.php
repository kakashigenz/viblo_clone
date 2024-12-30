<?php

namespace App\Exceptions;

use Exception;

class UnverifiedEmailException extends Exception
{
    public function __construct(string $message = "Email chưa được xác thực", int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
