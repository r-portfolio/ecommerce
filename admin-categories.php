<?php

declare(strict_types=1);

use Hcode\Model\Category;
use Hcode\Model\User;

// Categorias

$app->get('/admin/categories', function () {
    User::verifyLogin();
    $categories = Category::listAll();
    $page = new PageAdmin();
    $page->setTpl('categories', [
        'categories' => $categories,
    ]);
});

$app->get('/admin/categories/create', function () {
    User::verifyLogin();
    $page = new PageAdmin();
    $page->setTpl('categories-create');
});

$app->post('/admin/categories/create', function () {
    User::verifyLogin();
    $category = new Category();
    $category->setData($_POST);
    $category->saveCategory();
    header('Location:/admin/categories');
    exit;
});

$app->get('/admin/categories/:idcategory/delete', function ($idcategory) {
    User::verifyLogin();
    $category = new Category();
    $category->getCategory((int) $idcategory);
    $category->deleteCategory();
    header('Location: /admin/categories');
    exit;
});

$app->get('/admin/categories/:idcategory', function ($idcategory) {
    User::verifyLogin();
    $category = new Category();
    $category->getCategory((int) $idcategory);
    $page = new PageAdmin();
    $page->setTpl('categories-update', [
        'category' => $category->getValues(),
    ]);
});

// Atualiza categoria
$app->post('/admin/categories/:idcategory', function ($idcategory) {
    User::verifyLogin();
    $category = new Category();
    $category->getCategory((int) $idcategory);
    $category->setData($_POST);
    $category->saveCategory();
    header('location: /admin/categories');
    exit;
});

$app->get('/categories/:idcategory', function ($idcategory) {
    $category = new Category();
    $category->getCategory((int) $idcategory);
    $page = new Page();
    $page->setTpl('category', [
         'category' => $category->getValues(),
         'products' => [],
     ]);
});

// end Categorias