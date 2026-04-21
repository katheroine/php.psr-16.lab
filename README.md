# PHP.PSR-16.lab

Laboratory of PSR-16: Simple Cache.

> This repository is a standalone part of a larger project: **[PHP.lab](https://github.com/katheroine/php.lab)** — a curated knowledge base and laboratory for PHP engineering.

**Usage**

To run the example application with *Docker* use command:

```console
docker compose up -d
```

The following set-up steps are covered by the [`setup_container_dev_environment.sh`](./bin/setup_container_dev_environment.sh) script, that can be run from the local machine.

After creating the *Docker container* the *Composer dependencies* have to be defined and installed:

```console
docker exec --user root psr-16-example-app composer require --dev squizlabs/php_codesniffer --dev phpunit/phpunit \
&& docker exec --user root psr-16-example-app composer install
```

Tom make *PHP Code Sniffer commands* easily accessible run:

```console
docker exec --user root psr-16-example-app bash -c "
    ln -s /code/vendor/bin/phpcs /usr/local/bin/phpcs;
    ln -s /code/vendor/bin/phpcbf /usr/local/bin/phpcbf;
    ln -s /code/vendor/bin/phpunit /usr/local/bin/phpunit;
"
```

To run *PHP Code Sniffer* use command:

```console
docker exec psr-16-example-app /code/vendor/bin/phpcs
```

or, if the shortcut has been created:

```console
docker exec psr-16-example-app phpcs
```

To run *PHP Unit* use command:

```console
docker exec psr-16-example-app /code/vendor/bin/phpunit
```

or, if the shortcut has been created:

```console
docker exec psr-16-example-app phpunit
```

To update *Composer dependencies* use command:

```console
docker exec --user root psr-16-example-app composer update
```

To update *Composer autoloader* cache use use command:

```console
docker exec --user root psr-16-example-app composer dump-autoload
```

To login into the *Docker container* as default user use command:

```console
docker exec -it psr-16-example-app /bin/bash
```

To login into the *Docker container* as root user use command:

```console
docker exec --user root -it psr-16-example-app /bin/bash
```

**License**

This project is licensed under the GPL-3.0 - see [LICENSE](LICENSE).

## Overview

**Caching** is a common way to improve the performance of any project, and many libraries make use or could make use of it. Interoperability at this level means libraries can drop their own caching implementations and easily rely on the one given to them by the framework, or another dedicated cache library the user picked.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/meta/#1-summary)

PSR-6 solves this problem already, but in a rather formal and verbose way for what the most simple use cases need. This simpler approach aims to build a standardized layer of simplicity on top of the existing PSR-6 interfaces.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/meta/#2-why-bother)

It is independent of PSR-6 but has been designed to make compatibility with PSR-6 as straightforward as possible.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#11-introduction)

**Calling library** - The library or code that actually needs the cache services. This library will utilize caching services that implement this standard's interfaces, but will otherwise have no knowledge of the implementation of those caching services.

**Implementing library** - This library is responsible for implementing this standard in order to provide caching services to any Calling Library. The Implementing Library MUST provide a class implementing the Psr\SimpleCache\CacheInterface interface. Implementing Libraries MUST support at minimum TTL functionality as described below with whole-second granularity.

Definitions for Calling Library, Implementing Library, TTL, Expiration and Key are copied from PSR-6 as the same assumptions are true.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#12-definitions)

### Scope

#### Goals

* A simple interface for cache operations.
* Basic support for operations on multiple keys for performance (round-trip-time) reasons.
* Providing an adapter class that turns a PSR-6 implementation into a PSR-Simple-Cache one.
* It should be possible to expose both caching PSRs from a caching library.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/meta/#31-goals)

#### Non-goals

Solving all possible edge cases, PSR-6 does this well already.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/meta/#32-non-goals)

#### Approaches

The approach chosen here is very barebones by design, as it is to be used only by the most simple cases. It does not have to be implementable by all possible cache backends, nor be usable for all usages. It is merely a layer of convenience on top of PSR-6.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/meta/#4-approaches)

## Cache

**Cache** - An object that implements the `Psr\SimpleCache\CacheInterface` interface.

**Cache misses** - A *cache miss* will return `null` and therefore detecting if one stored `null` is not possible. This is the main deviation from *PSR-6*'s assumptions.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#12-definitions)

### TTL

Implementations MAY provide a mechanism for a user to specify a default TTL if one is not specified for a specific cache item. If no user-specified default is provided implementations MUST default to the maximum legal value allowed by the underlying implementation. If the underlying implementation does not support TTL, the user-specified TTL MUST be silently ignored.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#13-cache)

### Data

Implementing libraries MUST support all serializable PHP data types, including:

* *Strings* - Character strings of arbitrary size in any PHP-compatible encoding.
* *Integers* - All integers of any size supported by PHP, up to 64-bit signed.
* *Floats* - All signed floating point values.
* *Booleans* - True and False.
* *Null* - The null value (although it will not be distinguishable from a cache miss when reading it back out).
* *Arrays* - Indexed, associative and multidimensional arrays of arbitrary depth.
* *Objects* - Any object that supports lossless serialization and deserialization such that `$o == unserialize(serialize($o))`. Objects MAY leverage PHP's Serializable interface, `__sleep()` or `__wakeup()` magic methods, or similar language functionality if appropriate.

All data passed into the *implementing library* MUST be returned exactly as passed. That includes the variable type. That is, it is an error to return `(string) 5` if `(int) 5` was the value saved. *Implementing libraries* MAY use PHP's `serialize()`/`unserialize()` functions internally but are not required to do so. Compatibility with them is simply used as a baseline for acceptable object values.

If it is not possible to return the exact saved value for any reason, implementing libraries MUST respond with a cache miss rather than corrupted data.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#14-data)

## Cache storing time

**TTL** - *The Time To Live* (*TTL*) of an item is the amount of time between when that item is stored and it is considered stale. The *TTL* is normally defined by an integer representing time in seconds, or a `DateInterval` object.

**Expiration** - The actual time when an item is set to go stale. This is calculated by adding the *TTL* to the time when an object is stored.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#12-definitions)

An item with a 300 second *TTL* stored at `1:30:00` will have an expiration of `1:35:00`.

*Implementing libraries* MAY expire an item before its requested *expiration time*, but MUST treat an item as expired once its *expiration time* is reached. If a calling library asks for an item to be saved but does not specify an *expiration time*, or specifies a *null expiration time* or *TTL*, an implementing library MAY use a configured *default duration*. If no *default duration* has been set, the *implementing library* MUST interpret that as a *request to cache the item forever*, or for as long as the underlying implementation supports.

If a *negative or zero TTL* is provided, the item MUST be deleted from the cache if it exists, as it is expired already.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#12-definitions)

## Cache key

**Key** - A string of at least one character that uniquely identifies a cached item. Implementing libraries MUST support *keys* consisting of the characters `A-Z`, `a-z`, `0-9`, `_`, and . in any order in UTF-8 encoding and a length of up to `64` characters. *Implementing libraries* MAY support additional characters and encodings or longer lengths, but MUST support at least that minimum. *Libraries* are responsible for their own escaping of *key strings* as appropriate, but MUST be able to return the original unmodified *key string*. The following characters are reserved for future extensions and MUST NOT be supported by implementing libraries: `{}()/\@:`

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#12-definitions)

## Interface

**`CacheInterface`**

The cache interface defines the most basic operations on a collection of cache-entries, which entails basic reading, writing and deleting individual cache items.

In addition, it has methods for dealing with multiple sets of cache entries such as writing, reading or deleting multiple cache entries at a time. This is useful when you have lots of cache reads/writes to perform, and lets you perform your operations in a single call to the cache server cutting down latency times dramatically.

An instance of `CacheInterface` corresponds to a single collection of cache items with a single key namespace, and is equivalent to a *pool* in *PSR-6*. Different `CacheInterface` instances MAY be backed by the same datastore, but MUST be logically independent.

```php
<?php

namespace Psr\SimpleCache;

interface CacheInterface
{
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

**`CacheException`**

```php
<?php

namespace Psr\SimpleCache;

/**
 * Interface used for all types of exceptions thrown by the implementing library.
 */
interface CacheException
{
}
```

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#22-cacheexception)

**`InvalidArgumentException`**

```php
<?php

namespace Psr\SimpleCache;

/**
 * Exception interface for invalid cache arguments.
 *
 * When an invalid argument is passed, it must throw an exception which implements
 * this interface.
 */
interface InvalidArgumentException extends CacheException
{
}
```

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#23-invalidargumentexception)

## Throwable

The 2.0 release of the psr/simple-cache package updates Psr\SimpleCache\CacheException to extend \Throwable. This is considered a backwards compatible change for implementing libraries as of PHP 7.4.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/meta/#81-throwable)

## Type additions

The 2.0 release of the psr/simple-cache package includes scalar parameter types and increases the minimum PHP version to 8.0. This is considered a backwards compatible change for implementing libraries as PHP 7.2 introduces covariance for parameters. Any implementation of 1.0 is compatible with 2.0. For calling libraries, however, this reduces the types that they may pass (as previously any parameter that could be cast to string could be accepted) and as such requires incrementing the major version.

The 3.0 release includes return types. Return types break backwards compatibility for implementing libraries as PHP does not support return type widening.

Implementing libraries MAY add return types to their own packages at their discretion, provided that:

* the return types match those in the 3.0 package.
* the implementation specifies a minimum PHP version of 8.0.0 or later
* the implementation depends on "psr/simple-cache": "^2 || ^3" so as to exclude the untyped 1.0 version.

Implementing libraries MAY add parameter types to their own package in a new minor release, either at the same time as adding return types or in a subsequent release, provided that:

* the parameter types match or widen those in the 2.0 package
* the implementation specifies a minimum PHP version of 8.0 if using mixed or union types or later.
* the implementation depends on "psr/simple-cache": "^2 || ^3" so as to exclude the untyped 1.0 version.

Implementing libraries are encouraged, but not required to transition their packages toward the 3.0 version of the package at their earliest convenience.

Calling libraries are encouraged to ensure they are sending the correct types and to update their requirement to "psr/simple-cache": "^1 || ^2 || ^3" at their earliest convenience.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/meta/#82-type-additions)
