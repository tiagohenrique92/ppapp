<?php
namespace PPApp\Dto;

use PPApp\Dto\DtoAbstract;

class WalletDto extends DtoAbstract
{
    private $uuid;
    private $balance;

    public function __construct(string $uuid, float $balance)
    {
        $this->uuid = $uuid;
        $this->balance = $balance;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }
}
