<?php
namespace PPApp\Exceptions\Http\Request;

use Exception;
use Throwable;

class ParamNotFoundException extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        $code = 2;
        $message = "Param not found";
        parent::__construct($message, $code, $previous);
    }
}
