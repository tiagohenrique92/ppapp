<?php

namespace PPApp\Vos;

use PPApp\Vos\VoAbstract;
use PPApp\Models\Vos\WalletVo;

class UserVo extends VoAbstract
{
    private $id;
    private $uuid;
    private $name;
    private $email;
    private $password;
    private $type;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getType(): ?int
    {
        return $this->type;
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

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }
}
