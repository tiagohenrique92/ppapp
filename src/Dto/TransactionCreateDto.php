<?php
namespace PPApp\Dto;

use PPApp\Dto\DtoStandard;

class TransactionCreateDto extends DtoStandard
{
    private $payerUuid;
    private $payeeUuid;
    private $amount;

    public function __construct(string $payerUuid, string $payeeUuid, float $amount)
    {
        $this->payerUuid = $payerUuid;
        $this->payeeUuid = $payeeUuid;
        $this->amount = $amount;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getPayerUuid(): string
    {
        return $this->payerUuid;
    }

    public function getPayeeUuid(): string
    {
        return $this->payeeUuid;
    }
}
