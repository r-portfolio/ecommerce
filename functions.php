<?php

declare(strict_types=1);

function formatPrice(float $vlprice)
{
    return number_format($vlprice, 2, ',', '.');
}