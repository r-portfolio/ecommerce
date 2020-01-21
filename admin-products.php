<?php

declare(strict_types=1);

use Hcode\Model\Product;
use Hcode\Model\User;
use Hcode\PageAdmin;

$app->get('/admin/products', function () {
    User::verifyLogin();
    $products = Product::listAll();
    $page = new PageAdmin();
    $page->setTpl('products', [
        'products' => $products,
    ]);
});

$app->get('/admin/products/create', function () {
    User::verifyLogin();
    $page = new PageAdmin();
    $page->setTpl('products-create');
});
// Cria produtos no banco
$app->post('/admin/products/create', function () {
    User::verifyLogin();
    $product = new Product();
    $product->setData($_POST);
    $product->saveProduct();
    header('Location: /admin/products');
    exit;
});

$app->get('/admin/products/:idproduct', function ($idproduct) {
    User::verifyLogin();
    $product = new Product();
    $product->get((int) $idproduct);
    $page = new PageAdmin();
    $page->setTpl('products-update', [
        'product' => $product->getValues(),
    ]);
});

$app->post('/admin/products/:idproduct', function ($idproduct) {
    User::verifyLogin();
    $product = new Product();
    $product->get((int) $idproduct);
    $product->setData($_POST);
    $product->saveProduct();
    $product->setPhoto($_FILES['file']);
    header('Location: /admin/products');
    exit;
});

$app->get('/admin/products/:idproduct/delete', function ($idproduct) {
    User::verifyLogin();
    $product = new Product();
    $product->get((int) $idproduct);
    $product->delete();
    header('Location: /admin/products');
    exit;
});