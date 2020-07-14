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

namespace O2System\Cache\Adapters\Apcu;

// ------------------------------------------------------------------------

use O2System\Cache\Item;
use Psr\Cache\CacheItemInterface;
use O2System\Spl\Exceptions\Logic\InvalidArgumentException;

/**
 * Class ItemPool
 *
 * @package O2System\Cache\Adapters\Apc
 */
class ItemPool extends Adapter
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

        if (empty($keys) AND class_exists('APCUIterator', false)) {
            $apcIterator = new \APCUIterator();

            foreach ($apcIterator as $item) {
                $items[] = new Item(str_replace($this->prefixKey, '', $item[ 'key' ]), $item[ 'value' ]);
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

        $success = false;

        $metadata = apcu_fetch($this->prefixKey . $key, $success);

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

        return (bool)apcu_exists($this->prefixKey . $key);
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
        return apcu_clear_cache();
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

        return apcu_delete($this->prefixKey . $key);
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

        return apcu_store($this->prefixKey . $item->getKey(), $metadata, $metadata[ 'ttl' ]);
    }
}