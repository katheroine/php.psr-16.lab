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
    #[DataProvider('keyForbiddenCharactersProvider')]
    public function setDoesNotAllowForKeyBeingForbiddenCharacter(string $key)
    {
        $value = 'Some value.';

        $this->expectException(\Psr\SimpleCache\InvalidArgumentException::class);
        $this->cache->set($key, $value);
    }

    #[Test]
    #[DataProvider('keyWithForbiddenCharactersProvider')]
    public function setDoesNotAllowForKeyContainingForbiddenCharacter(string $key)
    {
        $value = 'Some value.';

        $this->expectException(\Psr\SimpleCache\InvalidArgumentException::class);
        $this->cache->set($key, $value);
    }

    #[Test]
    #[DataProvider('tooLongKeysProvider')]
    public function setDoesNotAllowForTooLongKey(string $key)
    {
        $value = 'Some value.';

        $this->expectException(\Psr\SimpleCache\InvalidArgumentException::class);
        $this->cache->set($key, $value);
    }

    #[Test]
    public function setDoesNotAllowForEmptyKey()
    {
        $key = '';
        $value = 'Some value.';

        $this->expectException(\Psr\SimpleCache\InvalidArgumentException::class);
        $this->cache->set($key, $value);
    }

    #[Test]
    #[DataProvider('properCachedValuesProvider')]
    public function setStoresValue(string $key, mixed $value): void
    {
        $result = $this->cache->set($key, $value);

        $this->assertTrue($result);
    }

    #[Test]
    #[DataProvider('keyForbiddenCharactersProvider')]
    public function getDoesNotAllowForKeyBeingForbiddenCharacter(string $key)
    {
        $this->expectException(\Psr\SimpleCache\InvalidArgumentException::class);
        $this->cache->get($key);
    }

    #[Test]
    #[DataProvider('keyWithForbiddenCharactersProvider')]
    public function getDoesNotAllowForKeyContainingForbiddenCharacter(string $key)
    {
        $this->expectException(\Psr\SimpleCache\InvalidArgumentException::class);
        $this->cache->get($key);
    }

    #[Test]
    #[DataProvider('tooLongKeysProvider')]
    public function getDoesNotAllowForTooLongKey(string $key)
    {
        $this->expectException(\Psr\SimpleCache\InvalidArgumentException::class);
        $this->cache->get($key);
    }

    #[Test]
    public function getDoesNotAllowForEmptyKey()
    {
        $key = '';

        $this->expectException(\Psr\SimpleCache\InvalidArgumentException::class);
        $this->cache->get($key);
    }

    #[Test]
    #[DataProvider('properCachedValuesProvider')]
    public function getReturnsStoredValue(string $key, mixed $expectedValue): void
    {
        $this->cache->set($key, $expectedValue);

        $actualValue = $this->cache->get($key);
        $this->assertSame($expectedValue, $actualValue);
    }

    #[Test]
    public function getReturnsProperOneFromStoredValues(): void
    {
        $chosenKey = 'other_key';
        $expectedValue = 'Other value';

        $this->cache->set('some_key', 'Some value');
        $this->cache->set($chosenKey, $expectedValue);
        $this->cache->set('another_key', 'Another value');

        $actualValue = $this->cache->get($chosenKey);
        $this->assertSame($expectedValue, $actualValue);
    }

    #[Test]
    public function getReturnsNullForNotStoredKey(): void
    {
        $this->cache->set('some_key', 'Some value');
        $this->cache->set('other_key', 'Other value');
        $this->cache->set('another_key', 'Another value');

        $result = $this->cache->get('unexistent_key');
        $this->assertNull($result);
    }

    #[Test]
    #[DataProvider('keyForbiddenCharactersProvider')]
    public function hasDoesNotAllowForKeyBeingForbiddenCharacter(string $key)
    {
        $this->expectException(\Psr\SimpleCache\InvalidArgumentException::class);
        $this->cache->has($key);
    }

    #[Test]
    #[DataProvider('keyWithForbiddenCharactersProvider')]
    public function hasDoesNotAllowForKeyContainingForbiddenCharacter(string $key)
    {
        $this->expectException(\Psr\SimpleCache\InvalidArgumentException::class);
        $this->cache->has($key);
    }

    #[Test]
    #[DataProvider('tooLongKeysProvider')]
    public function hasDoesNotAllowForTooLongKey(string $key)
    {
        $this->expectException(\Psr\SimpleCache\InvalidArgumentException::class);
        $this->cache->has($key);
    }

    #[Test]
    public function hasDoesNotAllowForEmptyKey()
    {
        $key = '';

        $this->expectException(\Psr\SimpleCache\InvalidArgumentException::class);
        $this->cache->has($key);
    }

    #[Test]
    #[DataProvider('properCachedValuesProvider')]
    public function hasReturnsTrueForStoredKey(string $key, mixed $value): void
    {
        $this->cache->set($key, $value);

        $result = $this->cache->has($key);
        $this->assertTrue($result);
    }

    #[Test]
    public function hasReturnsFalseForNotStoredKey(): void
    {
        $result = $this->cache->has('unexistent_key');
        $this->assertFalse($result);
    }

    #[Test]
    #[DataProvider('keyForbiddenCharactersProvider')]
    public function deleteDoesNotAllowForKeyBeingForbiddenCharacter(string $key)
    {
        $this->expectException(\Psr\SimpleCache\InvalidArgumentException::class);
        $this->cache->delete($key);
    }

    #[Test]
    #[DataProvider('keyWithForbiddenCharactersProvider')]
    public function deleteDoesNotAllowForKeyContainingForbiddenCharacter(string $key)
    {
        $this->expectException(\Psr\SimpleCache\InvalidArgumentException::class);
        $this->cache->delete($key);
    }

    #[Test]
    #[DataProvider('tooLongKeysProvider')]
    public function deleteDoesNotAllowForTooLongKey(string $key)
    {
        $this->expectException(\Psr\SimpleCache\InvalidArgumentException::class);
        $this->cache->delete($key);
    }

    #[Test]
    public function deleteDoesNotAllowForEmptyKey()
    {
        $key = '';

        $this->expectException(\Psr\SimpleCache\InvalidArgumentException::class);
        $this->cache->delete($key);
    }

    #[Test]
    #[DataProvider('properCachedValuesProvider')]
    public function deleteRemovesStoredEntry(string $key, mixed $value): void
    {
        $this->cache->set($key, $value);

        $result = $this->cache->delete($key);
        $this->assertTrue($result);
        $isExistent = $this->cache->has($key);
        $this->assertFalse($isExistent);
    }

    #[Test]
    public function deleteRemovesProperOneFromStoredEntries(): void
    {
        $someKey = 'some_key';
        $chosenKey = 'other_key';
        $anotherKey = 'another_key';

        $this->cache->set($someKey, 'Some value');
        $this->cache->set($chosenKey, 'Other value');
        $this->cache->set($anotherKey, 'Another value');

        $this->cache->delete($chosenKey);

        $this->assertTrue($this->cache->has($someKey));
        $this->assertFalse($this->cache->has($chosenKey));
        $this->assertTrue($this->cache->has($anotherKey));
    }

    #[Test]
    public function deleteRemovesNothingForNotStoredKey(): void
    {
        $someKey = 'some_key';
        $otherKey = 'other_key';
        $anotherKey = 'another_key';

        $this->cache->set($someKey, 'Some value');
        $this->cache->set($otherKey, 'Other value');
        $this->cache->set($anotherKey, 'Another value');

        $result = $this->cache->delete('unexistent_key');
        $this->assertFalse($result);

        $this->assertTrue($this->cache->has($someKey));
        $this->assertTrue($this->cache->has($otherKey));
        $this->assertTrue($this->cache->has($anotherKey));
    }

    #[Test]
    public function clearRemovesEverything(): void
    {
        $someKey = 'some_key';
        $otherKey = 'other_key';
        $anotherKey = 'another_key';

        $this->cache->set($someKey, 'Some value');
        $this->cache->set($otherKey, 'Other value');
        $this->cache->set($anotherKey, 'Another value');

        $result = $this->cache->clear();
        $this->assertTrue($result);

        $this->assertFalse($this->cache->has($someKey));
        $this->assertFalse($this->cache->has($otherKey));
        $this->assertFalse($this->cache->has($anotherKey));
    }

    #[Test]
    #[DataProvider('properKeysProvider')]
    public function handlesSingleDataWithProperKey(string $key)
    {
        $expectedValue = 'Some value.';

        $result = $this->cache->set($key, $expectedValue);

        $existence = $this->cache->has($key);
        $actualValue = $this->cache->get($key);

        $this->assertTrue($result);
        $this->assertTrue($existence);
        $this->assertEquals($expectedValue, $actualValue);

        $result = $this->cache->delete($key);

        $existence = $this->cache->has($key);
        $actualValue = $this->cache->get($key);

        $this->assertTrue($result);
        $this->assertFalse($existence);
        $this->assertNull($actualValue);
    }

    #[Test]
    #[DataProvider('multibyteUtf8KeysProvider')]
    public function handlesSingleDataWithProperMultibyteKey(string $key)
    {
        $expectedValue = 'Some value.';

        $result = $this->cache->set($key, $expectedValue);

        $existence = $this->cache->has($key);
        $actualValue = $this->cache->get($key);

        $this->assertTrue($result);
        $this->assertTrue($existence);
        $this->assertEquals($expectedValue, $actualValue);

        $result = $this->cache->delete($key);

        $existence = $this->cache->has($key);
        $actualValue = $this->cache->get($key);

        $this->assertTrue($result);
        $this->assertFalse($existence);
        $this->assertNull($actualValue);
    }

    #[Test]
    #[DataProvider('keyForbiddenCharactersProvider')]
    public function setMultipleDoesNotAllowForKeyBeingForbiddenCharacter(string $improperKey): void
    {
        $values = [
            'proper_key' => 'Some value.',
            $improperKey => 'Other value.',
        ];

        $this->expectException(\Psr\SimpleCache\InvalidArgumentException::class);
        $this->cache->setMultiple($values);
    }

    #[Test]
    #[DataProvider('keyWithForbiddenCharactersProvider')]
    public function setMultipleDoesNotAllowForKeyContainingForbiddenCharacter(string $improperKey)
    {
        $values = [
            'proper_key' => 'Some value.',
            $improperKey => 'Other value.',
        ];

        $this->expectException(\Psr\SimpleCache\InvalidArgumentException::class);
        $this->cache->setMultiple($values);
    }

    #[Test]
    #[DataProvider('tooLongKeysProvider')]
    public function setMultipleDoesNotAllowForTooLongKey(string $improperKey)
    {
        $values = [
            'proper_key' => 'Some value.',
            $improperKey => 'Other value.',
        ];

        $this->expectException(\Psr\SimpleCache\InvalidArgumentException::class);
        $this->cache->setMultiple($values);
    }

    #[Test]
    public function setMultipleDoesNotAllowForEmptyKey()
    {
        $improperKey = '';
        $values = [
            'proper_key' => 'Some value.',
            $improperKey => 'Other value.',
        ];

        $this->expectException(\Psr\SimpleCache\InvalidArgumentException::class);
        $this->cache->setMultiple($values);
    }

    #[Test]
    #[DataProvider('improperKeysProvider')]
    public function setMultipleDoesNotStoreAnythingWhenOneKeyIsInvalid(string $improperKey): void
    {
        $properKey = 'proper_key';
        $value = [
            $properKey => 'Some value',
            $improperKey => 'Other value',
        ];

        try {
            $this->cache->setMultiple($value);
        } catch (\PhpLab\StandardPsr16\InvalidArgumentException) {
            // On purpose.
        }

        $this->assertFalse($this->cache->has($properKey));
    }

    public static function improperKeysProvider(): array
    {
        return array_merge(
            self::keyForbiddenCharactersProvider(),
            self::keyWithForbiddenCharactersProvider(),
            self::tooLongKeysProvider(),
        );
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
     * Provides key containing not allowed characters
     * what is compliant with the PSR-16 specification rule:
     *
     * The following characters are reserved for future extensions
     * and MUST NOT be supported by implementing libraries: {}()/\@:
     *
     * @return array
     */
    public static function keyWithForbiddenCharactersProvider(): array
    {
        return [
            ['some{key'],
            ['some}key'],
            ['some(key'],
            ['some)key'],
            ['some/key'],
            ['some\\key'],
            ['some@key'],
            ['some:key'],
        ];
    }

    /**
     * Provides too long keys interpreted as improper
     * what is compliant with the PSR-16 specification rule:
     *
     * Implementing libraries MUST support keys consisting
     * of the characters A-Z, a-z, 0-9, _, and .
     * in any order in UTF-8 encoding and a length of up to 64 characters.
     *
     * @return array
     */
    public static function tooLongKeysProvider(): array
    {
        return [
            [str_repeat('a', 65)],
            [str_repeat('a', 66)],
            [str_repeat('a', 70)],
            [str_repeat('ą', 65)],
            [str_repeat('ą', 66)],
            [str_repeat('ą', 70)],
        ];
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
            ['some.key'],
            ['SOMEkey'],
            ['123key'],
            ['key3'],
            ['SOME-key_3'],
            [str_repeat('a', 64)],
        ];
    }

    /**
     * Provides multi-byte UTF-8 keys guaranteed as proper
     * what is compliant with the PSR-16 specification rule:
     *
     * Implementing libraries MUST support keys consisting
     * of the characters A-Z, a-z, 0-9, _, and .
     * in any order in UTF-8 encoding and a length of up to 64 characters.
     *
     * @return array
     */
    public static function multibyteUtf8KeysProvider(): array
    {
        return [
            ['Zażółć'], // Polish characters (multi-byte)
            ['你好'], // Chinese characters
            ['🚀key'], // emoji (4 bytes),
            [str_repeat('ą', 64)], // 64 multi-byte characters
        ];
    }

    /**
     * Provides key-value pairs of all data types
     * that implementing libraries MUST support,
     * compliant with the PSR-16 specification rule:
     *
     * Implementing libraries MUST support all serializable PHP data types,
     * including: Strings, Integers, Floats, Booleans, Null, Arrays, Objects.
     *
     * Note: Null cannot be distinguished from a cache miss.
     *
     * @return array
     */
    public static function properCachedValuesProvider(): array
    {
        return [
            'string'                => ['key', 'Some string value.'],
            'string_empty'          => ['key', ''],
            'string_multibyte'      => ['key', 'Zażółć gęślą jaźń'],
            'integer_positive'      => ['key', 42],
            'integer_negative'      => ['key', -42],
            'integer_zero'          => ['key', 0],
            'integer_max'           => ['key', PHP_INT_MAX],
            'integer_min'           => ['key', PHP_INT_MIN],
            'float_positive'        => ['key', 3.14],
            'float_negative'        => ['key', -3.14],
            'float_zero'            => ['key', 0.0],
            'float_max'             => ['key', PHP_FLOAT_MAX],
            'float_min'             => ['key', PHP_FLOAT_MIN],
            'boolean_true'          => ['key', true],
            'boolean_false'         => ['key', false],
            'array_indexed'         => ['key', [1, 2, 3]],
            'array_associative'     => ['key', ['foo' => 'bar', 'baz' => 42]],
            'array_nested'          => ['key', ['foo' => ['bar' => ['baz']]]],
            'array_empty'           => ['key', []],
            'object_serializable'   => ['key', new \stdClass()],
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
