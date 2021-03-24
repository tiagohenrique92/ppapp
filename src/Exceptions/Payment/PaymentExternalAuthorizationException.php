<?php
namespace PPApp\Exceptions\Payment;

use Exception;
use Throwable;

class PaymentExternalAuthorizationException extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        $code = 13;
        $message = "Failed to authorize the transaction";
        parent::__construct($message, $code, $previous);
    }
}
