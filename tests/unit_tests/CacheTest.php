<?php

/*
 * Copyright (C) 2026 Katarzyna Krasińska
 * PHP.PSR-16.lab - https://github.com/katheroine/php.psr-16.lab
 * Licensed under GPL-3.0 - see LICENSE.md
 */

declare(strict_types=1);

namespace PhpLab\StandardPsr16;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CacheTest extends TestCase
{
    /**
     * Instance of tested class.
     *
     * @var Cache
     */
    private Cache $cache;

    #[Test]
    #[DataProvider('properKeysProvider')]
    public function setsDataWithProperKey(string $key)
    {
        $expectedValue = 'Some value.';

        $this->cache->set($key, $expectedValue);

        $actualValue = $this->cache->get($key);

        $this->assertEquals($expectedValue, $actualValue);
    }

    /**
     * Provides random not allowed log levels.
     *
     * @return array
     */
    public static function properKeysProvider(): array
    {
        return [
            ['somekey'],
            ['some_key'],
            ['some-key'],
            ['SOMEkey'],
            ['123key'],
            ['key3'],
            ['SOME-key_3'],
        ];
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->cache = new Cache();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
    }
}
