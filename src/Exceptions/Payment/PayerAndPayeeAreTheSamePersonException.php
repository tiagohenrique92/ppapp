<?php
namespace PPApp\Exceptions\Payment;

use Exception;
use Throwable;

class PayerAndPayeeAreTheSamePersonException extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        $code = 6;
        $message = "The payer must be different from the payee";
        parent::__construct($message, $code, $previous);
    }
}
