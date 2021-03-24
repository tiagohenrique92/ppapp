<?php
namespace PPApp\Dto;

use PPApp\Dto\DtoAbstract;

class TransactionCreatedDto extends DtoAbstract
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
