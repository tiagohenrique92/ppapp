<?php

namespace PPApp\Vos;

use PPApp\Vos\UserVo;

class PersonUserVo extends UserVo
{
    private $cpf;

    public function getCpf(): ?string
    {
        return $this->cpf;
    }

    public function setCpf(string $cpf): self
    {
        $this->cpf = $cpf;
        return $this;
    }
}
