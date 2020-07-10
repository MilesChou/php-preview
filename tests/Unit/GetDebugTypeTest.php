<?php

namespace Tests\Unit;

use App\Http\Controllers\ConstructorPromotion;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @see https://wiki.php.net/rfc/get_debug_type
 */
class GetDebugTypeTest extends TestCase
{
    public function test_get_debug_type(): void
    {
        self::assertSame('stdClass', get_debug_type(new stdClass()));
        self::assertSame(ConstructorPromotion::class, get_debug_type(new ConstructorPromotion()));
    }
}
