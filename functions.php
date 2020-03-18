<?php

declare(strict_types=1);

use Hcode\Model\User;

/**
 * formatPrice.
 *
 * @param mixed $vlprice
 */
function formatPrice($vlprice)
{
    return number_format($vlprice, 2, ',', '.');
}

function checkLogin($inadmin = true)
{
    return User::checkLogin($inadmin);
}

function getUserName()
{
    $user = User::getFromSession();

    return $user->getdesperson();
}