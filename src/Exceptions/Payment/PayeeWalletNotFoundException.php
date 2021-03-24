<?php
namespace PPApp\Exceptions\Payment;

use Exception;
use Throwable;

class PayeeWalletNotFoundException extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        $code = 7;
        $message = "Payee's wallet not found";
        parent::__construct($message, $code, $previous);
    }
}
