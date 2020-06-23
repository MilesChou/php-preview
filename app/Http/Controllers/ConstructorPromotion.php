<?php

namespace App\Http\Controllers;

use App\ConstructorPromotionExample;

/**
 * @see https://wiki.php.net/rfc/constructor_promotion
 */
class ConstructorPromotion extends Controller
{
    public function __invoke()
    {
        return (new ConstructorPromotionExample(10))->getX();
    }
}
