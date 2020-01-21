<?php

declare(strict_types=1);

use Hcode\Page;

$app->get('/', function () {
    $page = new Page();
    $page->setTpl('index');
});