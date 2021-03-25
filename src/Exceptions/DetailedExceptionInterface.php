<?php
namespace PPApp\Exceptions;

use Throwable;

interface DetailedExceptionInterface
{
    public static function create(array $details = null, Throwable $previous = null): DetailedExceptionInterface;

    public function getCode();
    public function getDetails(): ?array;
    public function getMessage();
}
