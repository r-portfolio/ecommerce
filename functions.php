<?php

declare(strict_types=1);

/**
 * formatPrice.
 *
 * @param mixed $vlprice
 */
// Método na view
function formatPrice(float $vlprice)
{
    return number_format($vlprice, 2, ',', '.');
}