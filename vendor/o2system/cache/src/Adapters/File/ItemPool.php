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

namespace O2System\Cache\Adapters\File;

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
            throw new InvalidArgumentException('CACHE_E_INVALID_ARGUMENT_ARRAY');
        }

        $items = [];

        if (empty($keys)) {
            $directory = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($this->path),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            $cacheIterator = new \RegexIterator($directory, '/^.+\.cache/i', \RecursiveRegexIterator::GET_MATCH);

            foreach ($cacheIterator as $cacheFiles) {
                foreach ($cacheFiles as $cacheFile) {
                    $items[] = $this->getItem(pathinfo($cacheFile, PATHINFO_FILENAME));
                }
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
            throw new InvalidArgumentException('CACHE_E_INVALID_ARGUMENT_STRING');
        }

        $filename = $this->path . $key . '.cache';

        if (is_file($filename)) {
            $metadata = unserialize(file_get_contents($filename));

            if ($metadata[ 'ttl' ] > 0 AND time() > $metadata[ 'ctime' ] + $metadata[ 'ttl' ]) {
                unlink($filename);
            }

            return new Item($key, $metadata);
        }

        return new Item($key);
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
            throw new InvalidArgumentException('CACHE_E_INVALID_ARGUMENT_STRING');
        }

        $filename = $this->path . $key . '.cache';

        return (bool)is_file($filename);
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
        $directory = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->path),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $cacheIterator = new \RegexIterator($directory, '/^.+\.cache/i', \RecursiveRegexIterator::GET_MATCH);

        $isCleared = false;

        foreach ($cacheIterator as $cacheFiles) {
            foreach ($cacheFiles as $cacheFile) {
                if (false === ($isCleared = unlink($cacheFile))) {
                    return $isCleared;
                    break;
                }
            }
        }

        return $isCleared;
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
            throw new InvalidArgumentException('CACHE_E_INVALID_ARGUMENT_STRING');
        }

        $filename = $this->path . $key . '.cache';

        if (is_file($filename)) {
            return unlink($filename);
        }

        return false;
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
        $metadata = serialize($metadata);

        $path = $this->path . $this->prefixKey . $item->getKey() . '.cache';

        if ( ! $fp = @fopen($path, 'wb')) {
            return false;
        }

        flock($fp, LOCK_EX);

        for ($result = $written = 0, $length = strlen($metadata); $written < $length; $written += $result) {
            if (($result = fwrite($fp, substr($metadata, $written))) === false) {
                break;
            }
        }

        flock($fp, LOCK_UN);
        fclose($fp);

        return (bool)is_int($result);
    }
}