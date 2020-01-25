<?php

declare(strict_types=1);

namespace Hcode\Model;

use Hcode\DB\Sql;
use Hcode\Model;

class Cart extends Model
{
    public const SESSION = 'Cart';

    /**
     * getFromSessionCart.
     */
    public static function getFromSessionCart()
    {
        $cart = new self();
        if (isset($_SESSION[self::SESSION]) && (int) $_SESSION[self::SESSION]['idcart'] > 0) {
            $cart->get((int) $_SESSION[self::SESSION]['idcart']);
        } else {
            $cart->getFromSessionID();
            if (!(int) $cart->getidcart() > 0) {
                $data = [
                    'dessessionid' => session_id(),
                ];
                if (User::checkLogin(false)) {
                    $user = User::getFromSessionUser();
                    $data['iduser'] = $user->getiduser();
                }
                $cart->setData($data);
                $cart->save();
                $cart->setToSession();
            }
        }

        return $cart;
    }

    /**
     * setToSession.
     */
    // Set dados da sessão no banco
    public function setToSession()
    {
        $_SESSION[self::SESSION] = $this->getValues();
    }

    /**
     * getFromSessionID.
     */
    // Carrega sessão do carrinho de compras
    public function getFromSessionID()
    {
        $sql = new Sql();
        $results = $sql->select('SELECT * FROM tb_carts WHERE dessessionid = :dessessionid', [
            ':dessessionid' => session_id(),
        ]);
        if (\count($results) > 0) {
            $this->setData($results[0]);
        }
    }

    /**
     * get.
     *
     * @param mixed $idcart
     */
    public function get(int $idcart)
    {
        $sql = new Sql();
        $results = $sql->select('SELECT * FROM tb_carts WHERE idcart = :idcart', [
            ':idcart' => $idcart,
        ]);
        if (\count($results) > 0) {
            $this->setData($results[0]);
        }
    }

    /**
     * save.
     */
    public function save()
    {
        $sql = new Sql();
        $results = $sql->select('CALL sp_carts_save(:idcart, :dessessionid, :iduser, :deszipcode, :vlfreight, :nrdays)', [
            ':idcart' => $this->getidcart(),
            ':dessessionid' => $this->getdessessionid(),
            ':iduser' => $this->getiduser(),
            ':deszipcode' => $this->getdeszipcode(),
            ':vlfreight' => $this->getvlfreight(),
            ':nrdays' => $this->getnrdays(),
        ]);

        $this->setData($results[0]);
    }
}