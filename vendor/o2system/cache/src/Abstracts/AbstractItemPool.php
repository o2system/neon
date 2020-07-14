<?php
/**
 * This file is part of the O2System Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */

// ------------------------------------------------------------------------

namespace O2System\Cache\Abstracts;

// ------------------------------------------------------------------------

use O2System\Cache\Item;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;
use O2System\Spl\Exceptions\Logic\InvalidArgumentException;
use O2System\Spl\Iterators\ArrayIterator;

/**
 * Class AbstractItemPool
 *
 * @package O2System\Cache\Abstracts
 */
abstract class AbstractItemPool implements
    CacheItemPoolInterface,
    CacheInterface
{
    /**
     * AbstractItemPool::$storage
     *
     * Deferred Items Storage
     *
     * @var array
     */
    private $deferred = [];

    // ------------------------------------------------------------------------

    /**
     * Delete an item from the cache by its unique key.
     *
     * @param string $key The unique cache key of the item to delete.
     *
     * @return bool True if the item was successfully removed. False if there was an error.
     *
     * @throws \Psr\Cache\InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function delete($key)
    {
        return (bool)$this->deleteItem($key);
    }

    // ------------------------------------------------------------------------

    /**
     * CacheItemPoolInterface::saveDeferred
     *
     * Sets a cache item to be persisted later.
     *
     * @param CacheItemInterface $item
     *   The cache item to save.
     *
     * @return bool
     *   False if the item could not be queued or if a commit was attempted and failed. True otherwise.
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        $key = spl_object_id($item);

        if ( ! array_key_exists($key, $this->deferred)) {
            $this->deferred[] = $item;

            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * CacheItemPoolInterface::commit
     *
     * Persists any deferred cache items.
     *
     * @return bool
     *   True if all not-yet-saved items were successfully saved or there were none. False otherwise.
     */
    public function commit()
    {
        $items = $this->deferred;

        foreach ($items as $key => $item) {
            if ($this->save($item) === true) {
                unset($items[ $key ]);
            }
        }

        if (count($items) == 0) {
            $this->deferred = [];

            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Obtains multiple cache items by their unique keys.
     *
     * @param iterable $keys    A list of keys that can obtained in a single operation.
     * @param mixed    $default Default value to return for keys that do not exist.
     *
     * @return iterable A list of key => value pairs. Cache keys that do not exist or are stale will have $default as
     *                  value.
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getMultiple($keys, $default = null)
    {
        $result = new ArrayIterator();

        foreach ($keys as $key) {
            if ($this->has($key)) {
                $result[ $key ] = $this->get($key, $default);
            }
        }

        return $result;
    }

    // ------------------------------------------------------------------------

    /**
     * Determines whether an item is present in the cache.
     *
     * NOTE: It is recommended that has() is only to be used for cache warming type purposes
     * and not to be used within your live applications operations for get/set, as this method
     * is subject to a race condition where your has() will return true and immediately after,
     * another script can remove it making the state of your app out of date.
     *
     * @param string $key The cache item key.
     *
     * @return bool
     *
     * @throws \Psr\Cache\InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function has($key)
    {
        return (bool)$this->hasItem($key);
    }

    // ------------------------------------------------------------------------

    /**
     * Fetches a value from the cache.
     *
     * @param string $key     The unique key of this item in the cache.
     * @param mixed  $default Default value to return if the key does not exist.
     *
     * @return mixed The value of the item from the cache, or $default in case of cache miss.
     *
     * @throws \Psr\Cache\InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function get($key, $default = null)
    {
        if ($this->hasItem($key)) {
            return $this->getItem($key);
        }

        return $default;
    }

    // ------------------------------------------------------------------------

    /**
     * Persists a set of key => value pairs in the cache, with an optional TTL.
     *
     * @param iterable               $values A list of key => value pairs for a multiple-set operation.
     * @param null|int|\DateInterval $ttl    Optional. The TTL value of this item. If no value is sent and
     *                                       the driver supports TTL then the library may set a default value
     *                                       for it or let the driver take care of that.
     *
     * @return bool True on success and false on failure.
     */
    public function setMultiple($values, $ttl = null)
    {
        $result = [];

        foreach ($values as $key => $value) {
            if ($this->set($key, $value, $ttl)) {
                $result[ $key ] = true;
            }
        }

        return (bool)count($result) == count($values);
    }

    // ------------------------------------------------------------------------

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * @param string                 $key   The key of the item to store.
     * @param mixed                  $value The value of the item to store, must be serializable.
     * @param null|int|\DateInterval $ttl   Optional. The TTL value of this item. If no value is sent and
     *                                      the driver supports TTL then the library may set a default value
     *                                      for it or let the driver take care of that.
     *
     * @return bool True on success and false on failure.
     */
    public function set($key, $value, $ttl = null)
    {
        return $this->save(new Item($key, $value, $ttl));
    }

    // ------------------------------------------------------------------------

    /**
     * Deletes multiple cache items in a single operation.
     *
     * @param iterable $keys A list of string-based keys to be deleted.
     *
     * @return bool True if the items were successfully removed. False if there was an error.
     *
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \O2System\Spl\Exceptions\Logic\InvalidArgumentException
     */
    public function deleteMultiple($keys)
    {
        return (bool)$this->deleteItems($keys);
    }

    // ------------------------------------------------------------------------

    /**
     * CacheItemPoolInterface::deleteItems
     *
     * Removes multiple items from the pool.
     *
     * @param string[] $keys
     *   An array of keys that should be removed from the pool.
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\Logic\InvalidArgumentException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function deleteItems(array $keys)
    {
        if ( ! is_array($keys)) {
            throw new InvalidArgumentException('CACHE_E_INVALID_ARGUMENT_ARRAY');
        }

        foreach ($keys as $key) {
            if ($this->deleteItem($key) === false) {
                return false;
                break;
            }
        }

        return true;
    }
}