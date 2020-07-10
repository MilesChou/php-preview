<?php

namespace Tests\Unit;

use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

/**
 * @see https://wiki.php.net/rfc/add_str_starts_with_and_ends_with_functions
 * @see https://wiki.php.net/rfc/str_contains
 */
class StrFunctionTest extends TestCase
{
    /**
     * @before
     */
    public function check(): void
    {
        if (PHP_VERSION_ID < 80000) {
            self::markTestSkipped('PHP version is not >= 8');
        }
    }

    public function test_str_starts_with(): void
    {
        $this->assertSame(
            Str::startsWith('mileschou', 'miles'),
            str_starts_with('mileschou', 'miles')
        );
    }

    public function test_str_ends_with(): void
    {
        $this->assertSame(
            Str::startsWith('mileschou', 'chou'),
            str_starts_with('mileschou', 'chou')
        );
    }

    public function test_str_contains(): void
    {
        $this->assertSame(
            Str::contains('mileschou', 'esc'),
            str_contains('mileschou', 'esc')
        );
    }
}
