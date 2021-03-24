<?php
namespace PPApp\Exceptions\Payment;

use Exception;
use Throwable;

class PayerWalletNotFoundException extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        $code = 8;
        $message = "Payer's wallet not found";
        parent::__construct($message, $code, $previous);
    }
}
