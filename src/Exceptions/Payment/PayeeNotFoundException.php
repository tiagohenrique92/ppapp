<?php
namespace PPApp\Exceptions\Payment;

use Exception;
use PPApp\Exceptions\DetailedExceptionInterface;
use Throwable;

class PayeeNotFoundException extends Exception implements DetailedExceptionInterface
{
    private const CODE = 4;
    private const MESSAGE = "Payee not found";
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
