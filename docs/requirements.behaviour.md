# PSR-16 behaviour requirements

## Cached data

1. [REQUIRED] Test if the cached data of the following types is accepted.

Implementing libraries MUST support all *serializable PHP data types*, including:

* *Strings* - Character strings of arbitrary size in any PHP-compatible encoding.
* *Integers* - All integers of any size supported by PHP, up to 64-bit signed.
* *Floats* - All signed floating point values.
* *Booleans* - `True` and `False`.
* *Null* - The `null` value (although it will not be distinguishable from a *cache miss* when reading it back out).
* *Arrays* - Indexed, associative and multidimensional arrays of arbitrary depth.
* *Objects* - Any object that supports lossless serialization and deserialization such that `$o == unserialize(serialize($o))`. Objects MAY leverage PHP's `Serializable` interface, `__sleep()` or `__wakeup()` magic methods, or similar language functionality if appropriate.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#14-data)

1.1. Strings

1.1.1 Size

Character strings of arbitrary size [...]

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#14-data)

1.1.2. Encoding

[...] in any PHP-compatible encoding.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#14-data)

1.2. Integers

1.2.1. Size

All integers of any size supported by PHP, up to 64-bit signed.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#14-data)

1.3. Floats

All signed floating point values.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#14-data)

1.4. Booleans and null

`True` and `False`.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#14-data)

The `null` value (although it will not be distinguishable from a *cache miss* when reading it back out).

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#14-data)

1.5. Arrays

Indexed, associative and multidimensional arrays of arbitrary depth.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#14-data)

1.6. Objects

Any object that supports lossless serialization and deserialization such that `$o == unserialize(serialize($o))`.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#14-data)

Objects MAY leverage PHP's `Serializable` interface, `__sleep()` or `__wakeup()` magic methods, or similar language functionality if appropriate.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#14-data)

2. [REQUIRED] Test if the cached data is returned exactly the same as passed with both value and type and do not show the effects of the unproperly done serialization.

All data passed into the *implementing library* MUST be returned exactly as passed with both value and type type, (that is, it is an error to return `(string) 5` if `(int) 5` was the value saved).

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#14-data)

*Implementing libraries* MAY use PHP's `serialize()`/`unserialize()` functions internally.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#14-data)

3. [REQUIRED] Test both cases - caching null data and cache miss, give the same result.

If it is not possible to return the exact saved value for any reason, *implementing libraries* MUST respond with a *cache miss* rather than corrupted data.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#14-data)

## Cache key

1. [REQUIRED] Test key can consist of the allowed characters put together in any order

Implementing libraries MUST support *keys* consisting of the characters `A-Z`, `a-z`, `0-9`, `_`, and `.` in any order [...]

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#12-definitions)

2. [REQUIRED] Test key can be encoded in UTF-8

[...] in *UTF-8 encoding* [...]

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#12-definitions)

3. [REQUIRED] Test key can consist of allowed UTF-8 characters up to 64 characters length

[...]  and a length of up to `64` characters.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#12-definitions)

4. [Optional] Test key can consist additional characters other than strictly allowed ones

*Implementing libraries* MAY support additional characters [...]

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#12-definitions)

5. [Optional] Test key can be encodded in the additional encodings

[...] and encodings [...]

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#12-definitions)

6. [Optional] Test key can be longer than 64 characters

[...] or longer lengths [...]

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#12-definitions)

7. [REQUIRED] Test key are returned exactly the same as passed as a string and do not show the effects of the possible escaping

*Libraries* are responsible for their own escaping of *key strings* as appropriate, but MUST be able to return the original unmodified *key string*.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#12-definitions)

8. [REQUIRED] Test key cannot consist the forbidden characters

The following characters are reserved for future extensions and MUST NOT be supported by implementing libraries: `{}()/\@:`

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#12-definitions)

## Cache storing time

1. [REQUIRED] Test if the TTL is not supported it is silently ignored as an user input.

If the underlying implementation does not support TTL, the user-specified TTL MUST be silently ignored.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#13-cache)

2. [REQUIRED] Test if the TTL is supported it will be respected.

An item with a 300 second *TTL* stored at `1:30:00` will have an expiration of `1:35:00`.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#12-definitions)

*Implementing libraries* MAY expire an item before its requested *expiration time*, but MUST treat an item as expired once its *expiration time* is reached.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#12-definitions)

3. [OPTIONAL] Test if the default expiration time is possible for been set.

Implementations MAY provide a mechanism for a user to specify a *default TTL* if one is not specified for a specific *cache item*.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#13-cache)

3. [OPTIONAL] Test if the default expitation time is used when TTL is not explicitly specified or specified as null

If a *calling library* asks for an item to be saved but does not specify an *expiration time*, or specifies a *null expiration time* or *TTL*, an implementing library MAY use a configured *default duration*.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#12-definitions)

4. [REQUIRED] Test if data never goes stale when the TTL is not set as well as the default expiration time

If no user-specified default is provided implementations MUST default to the maximum legal value allowed by the underlying implementation.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#13-cache)

If no *default duration* has been set, the *implementing library* MUST interpret that as a *request to cache the item forever*, or for as long as the underlying implementation supports.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#12-definitions)

5. [REQUIRED] Test if negative or zero TTL results in data deletion

If a *negative or zero TTL* is provided, the item MUST be deleted from the cache if it exists, as it is expired already.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#12-definitions)
