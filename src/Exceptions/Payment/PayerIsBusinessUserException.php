<?php
namespace PPApp\Exceptions\Payment;

use Exception;
use Throwable;

class PayerIsBusinessUserException extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        $code = 5;
        $message = "The payer can't be a business user";
        parent::__construct($message, $code, $previous);
    }
}
