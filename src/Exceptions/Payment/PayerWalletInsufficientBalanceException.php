<?php
namespace PPApp\Exceptions\Payment;

use Exception;
use Throwable;

class PayerWalletInsufficientBalanceException extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        $code = 9;
        $message = "The payer's wallet has no sufficient balance";
        parent::__construct($message, $code, $previous);
    }
}
