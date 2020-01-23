<?php

declare(strict_types=1);

namespace Hcode;

class Model
{
    private $values = [];

    public function __call($name, $args)
    {
        $method = substr($name, 0, 3);
        $fieldName = substr($name, 3, \strlen($name));

        switch ($method) {
            case 'get':
                return (isset($this->values[$fieldName])) ? $this->values[$fieldName] : null;
            break;

            case 'set':
                $this->values[$fieldName] = $args[0];
            break;
        }
    }

    // Definir os valores do Banco de Dados no objeto
    public function setData($data = [])
    {
        foreach ($data as $key => $value) {
            $this->{'set'.$key}($value);
        }
    }

    // Busca valores no array de objetos
    public function getValues()
    {
        return $this->values;
    }
}