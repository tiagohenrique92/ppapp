<?php
namespace PPApp\Dto;

use PPApp\Dto\DtoAbstract;

class TransactionDto extends DtoAbstract
{
    private $uuid;
    private $idPayer;
    private $idPayee;
    private $amount;
    private $datetimeCreated;
    private $datetimeAuthorized;
    private $status;

    public function __construct(string $uuid, int $idPayer, int $idPayee, float $amount, string $datetimeCreated, string $datetimeAuthorized, int $status)
    {
        $this->uuid = $uuid;
        $this->idPayer = $idPayer;
        $this->idPayee = $idPayee;
        $this->amount = $amount;
        $this->datetimeCreated = $datetimeCreated;
        $this->datetimeAuthorized = $datetimeAuthorized;
        $this->status = $status;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function getDatetimeAuthorized(): ?string
    {
        return $this->datetimeAuthorized;
    }

    public function getDatetimeCreated(): ?string
    {
        return $this->datetimeCreated;
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

    public function getStatus(): ?int
    {
        return $this->status;
    }
}
