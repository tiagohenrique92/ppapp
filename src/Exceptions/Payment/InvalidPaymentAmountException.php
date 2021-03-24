<?php
namespace PPApp\Exceptions\Payment;

use Exception;
use Throwable;

class InvalidPaymentAmountException extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        $code = 10;
        $message = "Invalid payment amount";
        parent::__construct($message, $code, $previous);
    }
}
