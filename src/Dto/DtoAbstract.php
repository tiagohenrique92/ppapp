<?php
namespace PPApp\Dto;

use ReflectionClass;

abstract class DtoAbstract
{
    public function toJson(): string
    {
        $reflect = new ReflectionClass($this);
        $properties = $reflect->getProperties();
        $data = array();

        foreach ($properties as $property) {
            $getter = "get" . \ucfirst($property->getName());
            $data[$property->getName()] = $this->$getter();
        }

        return json_encode($data);
    }
}
