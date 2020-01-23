<?php

declare(strict_types=1);

use Hcode\Model\User;
use Hcode\PageAdmin;

$app->get('/admin/users', function () {
    User::verifyLogin();
    $users = User::listAll();
    $page = new PageAdmin();
    $page->setTpl('users', [
        'users' => $users,
    ]);
});
$app->get('/admin/users/create', function () {
    User::verifyLogin();
    $page = new PageAdmin();
    $page->setTpl('users-create');
});

// Deleta o usuário
$app->get('/admin/users/:iduser/delete', function ($iduser) {
    User::verifyLogin();
    $user = new User();
    $user->get((int) $iduser);
    $user->delete();
    header('location: /admin/users');
    exit;
});
// Renderiza dados do usuário para edição
$app->get('/admin/users/:iduser', function ($iduser) {
    User::verifyLogin();
    $user = new User();
    $user->get((int) $iduser);
    $page = new PageAdmin();
    $page->setTpl('users-update', [
        'user' => $user->getValues(),
    ]);
});

// Cria conta de usuário
$app->post('/admin/users/create', function () {
    User::verifyLogin();
    var_dump($_POST);
    $user = new User();
    $_POST['inadmin'] = (isset($_POST['inadmin'])) ? 1 : 0;
    $user->setData($_POST);
    $user->save();
    header('Location: /admin/users');
    exit;
});
// Realiza o update do usuário
$app->post('/admin/users/:iduser', function ($iduser) {
    User::verifyLogin();
    $user = new User();
    $_POST['inadmin'] = (isset($_POST['inadmin'])) ? 1 : 0;
    $user->get((int) $iduser);
    $user->setData($_POST);
    $user->update();
    header('Location: /admin/users');
    exit;
});