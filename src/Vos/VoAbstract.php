<?php
namespace PPApp\Vos;

abstract class VoAbstract 
{
    public function __construct(array $data = [])
    {
        $this->populate($data);
    }
    
    private function populate(array $data = [])
    {
        foreach ($data as $propertyName => $value) {
            $sufixAsArray = array_map(function($piece){
                return ucfirst($piece);
            }, explode("-", $propertyName));
            $sufix = join("", $sufixAsArray);
            $setter = "set{$sufix}";

            if (method_exists($this, $setter)) {
                $this->$setter($value);
            }
        }
    }
}
