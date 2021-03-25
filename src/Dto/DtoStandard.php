<?php
namespace PPApp\Dto;

use ReflectionClass;

class DtoStandard
{
    public function toArray(): array
    {
        $reflect = new ReflectionClass($this);
        $properties = $reflect->getProperties();
        $data = array();

        foreach ($properties as $property) {
            $getter = "get" . \ucfirst($property->getName());
            $data[$property->getName()] = $this->$getter();
        }

        return $data;
    }
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}
