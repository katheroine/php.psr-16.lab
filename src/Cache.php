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
    public function set(string $key, mixed $value): bool
    {
        $this->validateKey($key);

        return true;
    }

    public function get(string $key)
    {
        $this->validateKey($key);

        return 'Some value.';
    }

    public function has(string $key): bool
    {
        $this->validateKey($key);

        return true;
    }

    public function delete(string $key)
    {
        $this->validateKey($key);
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
