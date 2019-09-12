<?php
namespace Pepsite\Entity;

abstract class DBEntity
{
    public function exchangeArray($data)
    {
        foreach (array_keys(get_object_vars($this)) as $property) {
            $this->$property = $data[$property] ?? null;
        }
    }

//    public function __call($name, $arguments)
//    {
//        $method = substr($name, 0, 3);
//        $property = lcfirst(substr($name, 3));
//        if ($method === 'get') {
//            return $this->$property;
//        }
//        if ($method === 'set') {
//            $this->$property = $arguments[0];
//        }
//        return null;
//    }
}
