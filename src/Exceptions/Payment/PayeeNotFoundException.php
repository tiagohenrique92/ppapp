<?php
namespace PPApp\Exceptions\Payment;

use Exception;
use Throwable;

class PayeeNotFoundException extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        $code = 4;
        $message = "Payee not found";
        parent::__construct($message, $code, $previous);
    }
}
