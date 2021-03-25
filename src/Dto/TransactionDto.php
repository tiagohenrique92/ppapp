<?php
namespace PPApp\Dto;

use PPApp\Dto\DtoStandard;

class TransactionDto extends DtoStandard
{
    private $uuid;
    private $idPayer;
    private $idPayee;
    private $amount;
    private $createdAt;

    public function __construct(string $uuid, int $idPayer, int $idPayee, float $amount, string $createdAt)
    {
        $this->uuid = $uuid;
        $this->idPayer = $idPayer;
        $this->idPayee = $idPayee;
        $this->amount = $amount;
        $this->createdAt = $createdAt;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function getIdPayer(): ?int
    {
        return $this->idPayer;
    }

    public function getIdPayee(): ?int
    {
        return $this->idPayee;
    }
}
