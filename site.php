<?php

declare(strict_types=1);

use Hcode\Model\Category;
use Hcode\Model\Product;
use Hcode\Page;

$app->get('/', function () {
    $products = Product::listAll();
    $page = new Page();
    $page->setTpl('index', [
      'products' => Product::checkList($products),
    ]);
});

// Categoria da loja front
$app->get('/categories/:idcategory', function ($idcategory) {
    $category = new Category();
    $category->getCategory((int) $idcategory);
    $page = new Page();
    $page->setTpl('category', [
         'category' => $category->getValues(),
         'products' => Product::checkList($category->getProducts()),
     ]);
});