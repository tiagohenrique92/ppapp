<?php
namespace PPApp\Exceptions\User;

use Exception;
use Throwable;

class UserNotFoundException extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        $code = 11;
        $message = "User not found";
        parent::__construct($message, $code, $previous);
    }
}
