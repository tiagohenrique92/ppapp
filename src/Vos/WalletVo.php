<?php

namespace PPApp\Vos;

use PPApp\Vos\VoAbstract;

class WalletVo extends VoAbstract
{
    private $id;
    private $uuid;
    private $id_user;
    private $balance;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function getIdUser(): ?int
    {
        return $this->id_user;
    }

    public function getBalance(): ?float
    {
        return $this->balance;
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

    public function setIdUser(int $id_user): self
    {
        $this->id_user = $id_user;
        return $this;
    }

    public function setBalance(float $balance): self
    {
        $this->balance = $balance;
        return $this;
    }
}
