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

    /**
     * getProducts.
     *
     * @param mixed $related
     */
    // Verifica relação da categoria com produtos
    public function getProducts($related = true)
    {
        $sql = new Sql();
        if (true === $related) {
            return $sql->select('
				SELECT * FROM tb_products WHERE idproduct IN(
					SELECT a.idproduct
					FROM tb_products a
					INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
					WHERE b.idcategory = :idcategory
				);
			', [
                ':idcategory' => $this->getidcategory(),
            ]);
        }

        return $sql->select('
				SELECT * FROM tb_products WHERE idproduct NOT IN(
					SELECT a.idproduct
					FROM tb_products a
					INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
					WHERE b.idcategory = :idcategory
				);
			', [
                ':idcategory' => $this->getidcategory(),
            ]);
    }

    /**
     * getProductsPage.
     *
     * @param mixed $page
     * @param mixed $itemsPerPage
     */
    // Itens por paginas
    public function getProductsPage($page = 1, $itemsPerPage = 8)
    {
        $start = ($page - 1) * $itemsPerPage;

        $sql = new Sql();

        $results = $sql->select("
			SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_products a
			INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
			INNER JOIN tb_categories c ON c.idcategory = b.idcategory
			WHERE c.idcategory = :idcategory
			LIMIT $start, $itemsPerPage;
		", [
            ':idcategory' => $this->getidcategory(),
        ]);

        $resultTotal = $sql->select('SELECT FOUND_ROWS() AS nrtotal;');

        return [
            'data' => Product::checkList($results),
            'total' => (int) $resultTotal[0]['nrtotal'],
            'pages' => ceil($resultTotal[0]['nrtotal'] / $itemsPerPage),
        ];
    }

    /**
     * addProduct.
     *
     * @param mixed $product
     */
    public function addProduct(Product $product)
    {
        $sql = new Sql();
        $sql->query('INSERT INTO tb_productscategories (idcategory, idproduct) VALUES (:idcategory, :idproduct)', [
            ':idcategory' => $this->getidcategory(),
            ':idproduct' => $product->getidproduct(),
        ]);

        var_dump($sql);
    }

    /**
     * removeProduct.
     *
     * @param mixed $product
     */
    public function removeProduct(Product $product)
    {
        $sql = new Sql();
        $sql->query('DELETE FROM tb_productscategories WHERE  idcategory = :idcategory AND idproduct = :idproduct', [
            ':idcategory' => $this->getidcategory(),
            ':idproduct' => $product->getidproduct(),
        ]);
    }
}