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

namespace O2System\Cache\Adapters\Redis;

// ------------------------------------------------------------------------

use O2System\Cache\Item;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use O2System\Spl\Exceptions\Logic\InvalidArgumentException;

/**
 * Class ItemPool
 *
 * @package O2System\Cache\Adapters\File
 */
class ItemPool extends Adapter implements CacheItemPoolInterface
{
    /**
     * ItemPool::getItems
     *
     * Returns a traversable set of cache items.
     *
     * @param string[] $keys
     *   An indexed array of keys of items to retrieve.
     *
     * @throws InvalidArgumentException
     *   If any of the keys in $keys are not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return array|\Traversable
     *   A traversable collection of Cache Items keyed by the cache keys of
     *   each item. A Cache item will be returned for each key, even if that
     *   key is not found. However, if no keys are specified then an empty
     *   traversable MUST be returned instead.
     */
    public function getItems(array $keys = [])
    {
        if ( ! is_array($keys)) {
            throw new InvalidArgumentException('E_INVALID_ARGUMENT_ARRAY_CACHE_EXCEPTION');
        }

        $items = [];

        if (empty($keys)) {
            $allItems = $this->redis->getKeys('*');

            foreach ($allItems as $itemKey) {
                $items[] = $this->getItem(str_replace($this->prefixKey, '', $itemKey));
            }
        } elseif (count($keys)) {
            foreach ($keys as $key) {
                $items[] = $this->getItem($key);
            }
        }

        return $items;
    }

    // ------------------------------------------------------------------------

    /**
     * ItemPool::getKey
     *
     * Returns a Cache Item representing the specified key.
     *
     * This method must always return a CacheItemInterface object, even in case of
     * a cache miss. It MUST NOT return null.
     *
     * @param string $key
     *   The key for which to return the corresponding Cache Item.
     *
     * @throws InvalidArgumentException
     *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return CacheItemInterface
     *   The corresponding Cache Item.
     */
    public function getItem($key)
    {
        if ( ! is_string($key)) {
            throw new InvalidArgumentException('E_INVALID_ARGUMENT_STRING_CACHE_EXCEPTION');
        }

        $metadata = $this->redis->get($this->prefixKey . $key);
        $metadata = unserialize($metadata);

        return new Item($key, $metadata);
    }

    // ------------------------------------------------------------------------

    /**
     * ItemPool::hasItem
     *
     * Confirms if the cache contains specified cache item.
     *
     * Note: This method MAY avoid retrieving the cached value for performance reasons.
     * This could result in a race condition with CacheItemInterface::get(). To avoid
     * such situation use CacheItemInterface::isHit() instead.
     *
     * @param string $key
     *   The key for which to check existence.
     *
     * @throws InvalidArgumentException
     *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return bool
     *   True if item exists in the cache, false otherwise.
     */
    public function hasItem($key)
    {
        if ( ! is_string($key)) {
            throw new InvalidArgumentException('E_INVALID_ARGUMENT_STRING_CACHE_EXCEPTION');
        }

        return (bool)$this->redis->exists($this->prefixKey . $key);
    }

    // ------------------------------------------------------------------------

    /**
     * ItemPool::clear
     *
     * Deletes all items in the pool.
     *
     * @return bool
     *   True if the pool was successfully cleared. False if there was an error.
     */
    public function clear()
    {
        if (isset($this->config[ 'dbIndex' ])) {
            return $this->redis->flushDB();
        } else {
            return $this->redis->flushAll();
        }
    }

    // ------------------------------------------------------------------------

    /**
     * ItemPool::deleteItem
     *
     * Removes the item from the pool.
     *
     * @param string $key
     *   The key to delete.
     *
     * @throws InvalidArgumentException
     *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return bool
     *   True if the item was successfully removed. False if there was an error.
     */
    public function deleteItem($key)
    {
        if ( ! is_string($key)) {
            throw new InvalidArgumentException('E_INVALID_ARGUMENT_STRING_CACHE_EXCEPTION');
        }

        return (bool)($this->redis->delete($this->prefixKey . $key) === 1);
    }

    // ------------------------------------------------------------------------

    /**
     * ItemPool::save
     *
     * Persists a cache item immediately.
     *
     * @param CacheItemInterface $item
     *   The cache item to save.
     *
     * @return bool
     *   True if the item was successfully persisted. False if there was an error.
     */
    public function save(CacheItemInterface $item)
    {
        $metadata = $item->getMetadata();
        $metadata[ 'data' ] = $item->get();

        if ( ! $this->redis->set($this->prefixKey . $item->getKey(), serialize($metadata), $metadata[ 'ttl' ])) {
            return false;
        } elseif ($metadata[ 'ttl' ] > 0) {
            $this->redis->expireAt($this->prefixKey . $item->getKey(), $metadata[ 'etime' ]);
        }

        return true;
    }
}