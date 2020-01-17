<?php

declare(strict_types=1);

namespace Hcode\Model;

use Hcode\DB\Sql;
use Hcode\Model;

class Category extends Model
{
    public static function listAll()
    {
        $sql = new Sql();

        return $sql->select('SELECT * FROM tb_categories ORDER BY descategory');
    }

    public function saveCategory()
    {
        $sql = new Sql();

        $results = $sql->select('CALL sp_categories_save(:idcategory, :descategory)', [
         ':idcategory' => $this->getidcategory(),
         ':descategory' => $this->getdescategory(),
        ]);

        $this->setData($results[0]);
    }

    /**
     * get.
     *
     * @param mixed $idcategory
     * @param int   $idcategory recebe id da categoria
     */
    public function get($idcategory)
    {
        $sql = new Sql();

        $results = $sql->select('SELECT * FROM tb_categories WHERE idcategory = :idcategory', [
           ':idcategory' => $idcategory,
         ]);
        // setData coloco os dados no objeto
        $this->setData($results[0]);
    }

    /**
     * delete.
     */
    // Delete categoria
    public function delete()
    {
        $sql = new Sql();
        $sql->query('DELETE  FROM tb_categories WHERE idcategory = :idcategory', [
          'idcategory' => $this->getidcategory(),
        ]);
    }
}