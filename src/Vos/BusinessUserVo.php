<?php
namespace PPApp\Models\Vos;

use PPApp\Vos\UserVo;

class BusinessUserVo extends UserVo
{
    private $cnpj;

    public function getCnpj(): ?string
    {
        return $this->cnpj;
    }

    public function setCnpj(string $cnpj): self
    {
        $this->cnpj = $cnpj;
        return $this;
    }
}
