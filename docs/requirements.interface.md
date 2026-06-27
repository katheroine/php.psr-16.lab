# PSR-16 interface requirements

## Interface `CacheInterface`

### Methods `get` and `set`

```php
/**
     * Fetches a value from the cache.
     *
     * @param string $key     The unique key of this item in the cache.
     * @param mixed  $default Default value to return if the key does not exist.
     *
     * @return mixed The value of the item from the cache, or $default in case of cache miss.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function get($key, $default = null);
```

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#21-cacheinterface)

```php
/**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * @param string                 $key   The key of the item to store.
     * @param mixed                  $value The value of the item to store. Must be serializable.
     * @param null|int|\DateInterval $ttl   Optional. The TTL value of this item. If no value is sent and
     *                                      the driver supports TTL then the library may set a default value
     *                                      for it or let the driver take care of that.
     *
     * @return bool True on success and false on failure.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function set($key, $value, $ttl = null);
```

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#21-cacheinterface)

1. Method `get` should return `null` when no cache has been set yet.

[REQUIRED] Test both cases - caching null data and cache miss, give the same result.

If it is not possible to return the exact saved value for any reason, *implementing libraries* MUST respond with a *cache miss* rather than corrupted data.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#14-data)

2. Method `set` should accept all the allowed data types.

[REQUIRED] Test if the cached data of the following types is accepted.

Implementing libraries MUST support all *serializable PHP data types*, including:

* *Strings* - Character strings of arbitrary size in any PHP-compatible encoding.
* *Integers* - All integers of any size supported by PHP, up to 64-bit signed.
* *Floats* - All signed floating point values.
* *Booleans* - `True` and `False`.
* *Null* - The `null` value (although it will not be distinguishable from a *cache miss* when reading it back out).
* *Arrays* - Indexed, associative and multidimensional arrays of arbitrary depth.
* *Objects* - Any object that supports lossless serialization and deserialization such that `$o == unserialize(serialize($o))`. Objects MAY leverage PHP's `Serializable` interface, `__sleep()` or `__wakeup()` magic methods, or similar language functionality if appropriate.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#14-data)

3. Method `set` should set a value and `get` should return the same value of the same type with no signas of the serialization.

[REQUIRED] Test if the cached data is returned exactly the same as passed with both value and type and do not show the effects of the serialization done.

All data passed into the *implementing library* MUST be returned exactly as passed with both value and type type, (that is, it is an error to return `(string) 5` if `(int) 5` was the value saved).

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#14-data)



```php
<?php

namespace Psr\SimpleCache;

interface CacheInterface
{
    /**
     * Delete an item from the cache by its unique key.
     *
     * @param string $key The unique cache key of the item to delete.
     *
     * @return bool True if the item was successfully removed. False if there was an error.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function delete($key);

    /**
     * Wipes clean the entire cache's keys.
     *
     * @return bool True on success and false on failure.
     */
    public function clear();

    /**
     * Obtains multiple cache items by their unique keys.
     *
     * @param iterable $keys    A list of keys that can obtained in a single operation.
     * @param mixed    $default Default value to return for keys that do not exist.
     *
     * @return iterable A list of key => value pairs. Cache keys that do not exist or are stale will have $default as value.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if $keys is neither an array nor a Traversable,
     *   or if any of the $keys are not a legal value.
     */
    public function getMultiple($keys, $default = null);

    /**
     * Persists a set of key => value pairs in the cache, with an optional TTL.
     *
     * @param iterable               $values A list of key => value pairs for a multiple-set operation.
     * @param null|int|\DateInterval $ttl    Optional. The TTL value of this item. If no value is sent and
     *                                       the driver supports TTL then the library may set a default value
     *                                       for it or let the driver take care of that.
     *
     * @return bool True on success and false on failure.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if $values is neither an array nor a Traversable,
     *   or if any of the $values are not a legal value.
     */
    public function setMultiple($values, $ttl = null);

    /**
     * Deletes multiple cache items in a single operation.
     *
     * @param iterable $keys A list of string-based keys to be deleted.
     *
     * @return bool True if the items were successfully removed. False if there was an error.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if $keys is neither an array nor a Traversable,
     *   or if any of the $keys are not a legal value.
     */
    public function deleteMultiple($keys);

    /**
     * Determines whether an item is present in the cache.
     *
     * NOTE: It is recommended that has() is only to be used for cache warming type purposes
     * and not to be used within your live applications operations for get/set, as this method
     * is subject to a race condition where your has() will return true and immediately after,
     * another script can remove it, making the state of your app out of date.
     *
     * @param string $key The cache item key.
     *
     * @return bool
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function has($key);
}
```

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#21-cacheinterface)
