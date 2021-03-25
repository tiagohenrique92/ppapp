<?php
namespace PPApp\Dto;

use PPApp\Dto\DtoStandard;

class TransactionCreatedDto extends DtoStandard
{
    private $uuid;

    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }
}
