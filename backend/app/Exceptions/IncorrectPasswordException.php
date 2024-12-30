<?php

namespace App\Exceptions;

use Exception;

class IncorrectPasswordException extends Exception
{
    public function __construct(string $message = "Mật khẩu không chính xác", int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
