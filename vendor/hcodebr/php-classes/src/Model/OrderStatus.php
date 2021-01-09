<?php

declare(strict_types=1);

namespace Hcode\Model;

use Hcode\DB\Sql;
use Hcode\Model;

// TODO melhorar este código
class OrderStatus extends Model
{
    public const EM_ABERTO = 1;
    public const AGUARDANDO_PAGAMENTO = 2;
    public const PAGO = 3;
    public const ENTREGUE = 4;

    public static function listAll()
    {
        $sql = new Sql();

        return $sql->select('SELECT * FROM tb_ordersstatus ORDER BY desstatus');
    }
}