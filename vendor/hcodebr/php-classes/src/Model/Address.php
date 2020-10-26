<?php

declare(strict_types=1);

namespace Hcode\Model;

use Hcode\DB\Sql;
use Hcode\Model;

class Address extends Model
{
    public const SESSION_ERROR = 'AddressError';

    public static function getCEP($nrcep)
    {
        $nrcep = str_replace('-', '', $nrcep);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://viacep.com.br/ws/$nrcep/json/");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $data = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return $data;
    }

    public function loadFromCEP($nrcep)
    {
        $data = self::getCEP($nrcep);

        if (isset($data['logradouro']) && $data['logradouro']) {
            $this->setdesaddress($data['logradouro']);
            $this->setdescomplement($data['complemento']);
            $this->setdesdistrict($data['bairro']);
            $this->setdescity($data['localidade']);
            $this->setdesstate($data['uf']);
            $this->setdescountry('Brasil');
            $this->setdeszipcode($nrcep);
        }
    }

    public function saveAddress()
    {
        $sql = new Sql();
        $results = $sql->select('CALL sp_addresses_save
        (:idaddress, :idperson,:desaddress, :desnumber, :descomplement, :descity,:desstate,:descountry,:deszipcode,:desdistrict)', [
         ':idaddress' => $this->getidaddress(),
         ':idperson' => $this->getidperson(),
         ':desaddress' => utf8_decode($this->getdesaddress()),
         ':desnumber' => utf8_decode($this->getdesnumber()),
         ':descomplement' => utf8_decode($this->getdescomplement()),
         ':descity' => utf8_decode($this->getdescity()),
         ':desstate' => utf8_decode($this->getdesstate()),
         ':descountry' => utf8_decode($this->getdescountry()),
         ':deszipcode' => $this->getdeszipcode(),
         ':desdistrict' => $this->getdesdistrict(),
       ]);

        if (\count($results) > 0) {
            $this->setData($results[0]);
        }
    }

    public static function setMsgError($msg)
    {
        $_SESSION[self::SESSION_ERROR] = $msg;
    }

    public static function getMsgError()
    {
        $msg = (isset($_SESSION[self::SESSION_ERROR])) ? $_SESSION[self::SESSION_ERROR] : '';
        self::cleartMsgError();

        return $msg;
    }

    public static function cleartMsgError()
    {
        $_SESSION[self::SESSION_ERROR] = null;
    }

    public function updateFreight()
    {
        if ('' != $this->getdeszipcode()) {
            $this->setFreight($this->getdeszipcode());
        }
    }
}