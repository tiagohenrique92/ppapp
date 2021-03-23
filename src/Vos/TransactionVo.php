<?php

namespace PPApp\Vos;

use PPApp\Vos\VoAbstract;

class TransactionVo extends VoAbstract {
    private $id;
    private $uuid;
    private $datetime_created;
    private $datetime_authorized;
    private $amount;
    private $id_payer;
    private $id_payee;
    private $status;
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getUuid(): ?string
    {
        return $this->uuid;
    }
    
    public function getDatetimeAuthorized(): ?string
    {
        return $this->datetime_authorized;;
    }
    
    public function getDatetimeCreated(): ?string
    {
        return $this->datetime_created;
    }
    
    public function getAmount(): ?float
    {
        return $this->amount;
    }
    
    public function getIdPayer(): ?int
    {
        return $this->id_payer;
    }
    
    public function getIdPayee(): ?int
    {
        return $this->id_payee;
    }
    
    public function getStatus(): ?int
    {
        return $this->status;
    }
    
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }
    
    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }
    
    public function setDatetimeAuthorized(string $datetime_authorized): self
    {
        $this->datetime_authorized = $datetime_authorized;
        return $this;
    }
    
    public function setDatetimeCreated(string $datetime_created): self
    {
        $this->datetime_created = $datetime_created;
        return $this;
    }
    
    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }
    
    public function setIdPayer(int $id_payer): self
    {
        $this->id_payer = $id_payer;
        return $this;
    }
    
    public function setIdPayee(int $id_payee): self
    {
        $this->id_payee = $id_payee;
        return $this;
    }
    
    public function setStatus(int $status): self
    {
        $ths->status = $status;
        return $this;
    }
}