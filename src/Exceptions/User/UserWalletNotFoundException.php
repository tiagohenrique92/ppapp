<?php
namespace PPApp\Exceptions\User;

use Exception;
use Throwable;

class UserWalletNotFoundException extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        $code = 12;
        $message = "User's wallet not found";
        parent::__construct($message, $code, $previous);
    }
}
