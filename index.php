<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

$app = new \Slim\Slim();

$app->config('debug', true);

$app->get('/', function () {
    echo 'App iniciado com sucesso!';
});

$app->run();