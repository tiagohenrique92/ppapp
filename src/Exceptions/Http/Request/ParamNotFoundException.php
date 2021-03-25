<?php
namespace PPApp\Exceptions\Http\Request;

use Exception;
use PPApp\Exceptions\DetailedExceptionInterface;
use Throwable;

class ParamNotFoundException extends Exception implements DetailedExceptionInterface
{
    private const CODE = 2;
    private const MESSAGE = "Param not found";
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
