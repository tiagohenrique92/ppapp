<?php
namespace PPApp\Exceptions\Payment;

use Exception;
use PPApp\Exceptions\DetailedExceptionInterface;
use Throwable;

class PayerWalletInsufficientBalanceException extends Exception implements DetailedExceptionInterface
{
    private const CODE = 9;
    private const MESSAGE = "The payer's wallet has no sufficient balance";
    private $details;

    public static function create(array $details = null, Throwable $previous = null): DetailedExceptionInterface
    {
        $ex = new self(self::MESSAGE, self::CODE, $previous);
        $ex->details = $details;
        return $ex;
    }

    public function getDetails(): ?array
    {
        return $this->details;
    }
}
