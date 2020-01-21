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
        self::updateFile();
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

        self::updateFile();
    }

    // Cria menu categoria dinamicamente
    public static function updateFile()
    {
        $categories = self::listAll();

        $html = [];
        foreach ($categories as $row) {
            array_push($html, '<li><a href="/categories/'.$row['idcategory'].'">'.$row['descategory'].'</a></li>');
        }

        file_put_contents($_SERVER['DOCUMENT_ROOT'].\DIRECTORY_SEPARATOR.'views'.\DIRECTORY_SEPARATOR.
        'categories-menu.html', implode('', $html));
    }

    /**
     * get.
     *
     * @param mixed $idcategory
     * @param int   $idcategory recebe id da categoria
     */
    public function getCategory($idcategory)
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
    public function deleteCategory()
    {
        $sql = new Sql();
        $sql->query('DELETE  FROM tb_categories WHERE idcategory = :idcategory', [
          'idcategory' => $this->getidcategory(),
        ]);
    }
}