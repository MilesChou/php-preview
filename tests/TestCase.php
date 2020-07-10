<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @before
     */
    public function check(): void
    {
        if (PHP_VERSION_ID < 80000) {
            self::markTestSkipped('PHP version is not >= 8');
        }
    }
}
