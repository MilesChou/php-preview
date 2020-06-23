<?php

namespace App;

/**
 * @see https://wiki.php.net/rfc/constructor_promotion
 */
class ConstructorPromotionExample
{
    public function __construct(private int $x)
    {
    }

    public function getX(): int
    {
        return $this->x;
    }
}
