<?php

/*
 * Copyright (C) 2026 Katarzyna Krasińska
 * PHP.PSR-16.lab - https://github.com/katheroine/php.psr-16.lab
 * Licensed under GPL-3.0 - see LICENSE.md
 */

declare(strict_types=1);

namespace PhpLab\StandardPsr16;

class Cache
{
    private array $cache = [];

    public function set(string $key, mixed $value): bool
    {
        $this->validateKey($key);
        $this->cache[$key] = $value;

        return true;
    }

    public function setMultiple(iterable $values): bool
    {
        $values = self::unifyMultipleArgumentType($values);
        $this->validateKeysOfValues($values);

        $this->cache = array_merge(
            $this->cache,
            $values
        );

        return true;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->validateKey($key);

        if (! key_exists($key, $this->cache)) {
            return $default;
        }

        return $this->cache[$key];
    }

    public function getMultiple(iterable $keys): iterable
    {
        $keys = self::unifyMultipleArgumentType($keys);
        $this->validateKeys($keys);

        $values = [];

        foreach ($keys as $key) {
            $values[$key] = $this->cache[$key];
        }

        return $values;
    }

    public function has(string $key): bool
    {
        $this->validateKey($key);

        if (! array_key_exists($key, $this->cache)) {
            return false;
        }

        return true;
    }

    public function delete(string $key): bool
    {
        $this->validateKey($key);

        if (! key_exists($key, $this->cache)) {
            return false;
        }

        unset($this->cache[$key]);

        return true;
    }

    public function clear(): bool
    {
        $this->cache = [];

        return true;
    }

    private static function unifyMultipleArgumentType(iterable $values): array
    {
        return (is_array($values) ? $values : iterator_to_array($values));
    }

    /**
     * Checks if each value key is compliant with the PSR-16 specification.
     */
    private function validateKeysOfValues(array $values): void
    {
        array_map(fn ($key) =>
            $this->validateKey($key),
            array_keys($values)
        );
    }

    /**
     * Checks if each key is compliant with the PSR-16 specification.
     */
    private function validateKeys(array $keys)
    {
        array_map(fn ($key) =>
            $this->validateKey($key),
            $keys
        );
    }

    /**
     * Checks if key it compliant with the PSR-16 specification rule:
     *
     * Implementing libraries MUST support keys consisting
     * of the characters A-Z, a-z, 0-9, _, and .
     * in any order in UTF-8 encoding and a length of up to 64 characters.
     */
    private function validateKey(string $key): void
    {
        if (
            empty($key)
            || mb_strlen($key) > 64
            || strpbrk($key, '{}()/\@:') !== false
        ) {
            throw new InvalidArgumentException();
        }
    }
}
