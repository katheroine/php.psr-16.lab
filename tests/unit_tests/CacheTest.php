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

    #[Test]
    #[DataProvider('keyForbiddenCharactersProvider')]
    public function doesNotAllowForKeyContainingForbiddenCharacter(string $key)
    {
        $value = 'Some value.';

        $this->expectException(\Psr\SimpleCache\InvalidArgumentException::class);
        $this->cache->set($key, $value);
    }

    /**
     * Provides keys guaranteed as proper
     * what is compliant with the PSR-16 specification rule:
     *
     * Implementing libraries MUST support keys consisting
     * of the characters A-Z, a-z, 0-9, _, and .
     * in any order in UTF-8 encoding and a length of up to 64 characters.
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
            [str_repeat('a', 64)],
        ];
    }

    /**
     * Provides key not allowed characters
     * what is compliant with the PSR-16 specification rule:
     *
     * The following characters are reserved for future extensions
     * and MUST NOT be supported by implementing libraries: {}()/\@:
     *
     * @return array
     */
    public static function keyForbiddenCharactersProvider(): array
    {
        return [
            ['{'],
            ['}'],
            ['('],
            [')'],
            ['/'],
            ['\\'],
            ['@'],
            [':'],
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
