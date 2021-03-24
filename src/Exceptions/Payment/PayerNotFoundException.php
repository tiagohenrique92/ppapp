<?php
namespace PPApp\Exceptions\Payment;

use Exception;
use Throwable;

class PayerNotFoundException extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        $code = 3;
        $message = "Payer not found";
        parent::__construct($message, $code, $previous);
    }
}
