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
    public function set(string $key, mixed $value)
    {
        if (strpbrk($key, '{}()/\@:') !== false) {
            throw new InvalidArgumentException();
        }

        if (strlen($key) > 63) {
            throw new InvalidArgumentException();
        }
    }

    public function get(string $key)
    {
        return 'Some value.';
    }
}
