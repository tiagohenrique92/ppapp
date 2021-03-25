<?php
namespace PPApp\Dto;

use PPApp\Dto\DtoStandard;

class UserDto extends DtoStandard
{
    private $uuid;
    private $name;
    private $email;
    private $password;
    private $type;

    public function __construct(string $uuid, string $name, string $email, string $password, int $type)
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->type = $type;
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
}
