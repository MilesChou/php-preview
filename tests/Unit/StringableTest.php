<?php

namespace Tests\Unit;

use Illuminate\Support\Stringable as LaravelStringable;
use Stringable;
use Tests\TestCase;

class StringableTest extends TestCase
{
    public function test_stringable(): void
    {
        $func = function (Stringable $string): Stringable {
            return $string;
        };

        // Cannot use string type
        // $this->assertSame('miles', (string)$func('miles'));

        // Use Laravel Stringable
        $this->assertSame('miles', (string)$func(new LaravelStringable('miles')));

        // Implements PHP 8 Stringable
        $this->assertSame('miles', (string)$func(new class() implements Stringable {
            public function __toString()
            {
                return 'miles';
            }
        }));
    }
}
