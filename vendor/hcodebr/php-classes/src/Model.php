<?php

declare(strict_types=1);

namespace Hcode;

class Model
{
    private $values = [];

    /**
     * __call.
     *
     * @param string $name recebe id do usuario
     * @param string $args recebe id do perfil
     */
    public function __call($name, $args)
    {
        $method = substr($name, 0, 3);
        $fieldName = substr($name, 3, \strlen($name));

        switch ($method) {
          case 'get':
               return $this->values[$fieldName];
              break;

              case 'set':
                $this->values[$fieldName] = $args[0];
              break;
      }
    }

    /**
     * setData.
     *
     * @param mixed $data
     */
    public function setData($data = [])
    {
        foreach ($data as $key => $value) {
            $this->{'set'.$key}($value);
        }
    }

    /**
     * getValues.
     *
     * @return dados da sessão
     */
    public function getValues()
    {
        return $this->values;
    }
}
