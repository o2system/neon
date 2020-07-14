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

namespace O2System\Cache;

// ------------------------------------------------------------------------

use Psr\Cache\CacheItemInterface;

/**
 * Class Item
 *
 * @package O2System\Cache\Collections
 */
class Item implements CacheItemInterface
{
    /**
     * Item::$key
     *
     * Item Key
     *
     * @var string
     */
    protected $key;

    /**
     * Item::$value
     *
     * Item Value
     *
     * @var mixed|null
     */
    protected $value = null;

    /**
     * Item::$isHit
     *
     * Item is hit flag
     *
     * @var bool
     */
    protected $isHit = false;

    /**
     * Item::$createdAt
     *
     * Item Creation Time
     *
     * @var \DateTimeInterface
     */
    protected $createdAt;

    /**
     * Item::$expiresAt
     *
     * Item Expiration Time
     *
     * @var \DateTimeInterface
     */
    protected $expiresAt;

    /**
     * Item::$expiresAfter
     *
     * Item Expiration Time Interval
     *
     * @var int|\DateInterval
     */
    protected $expiresAfter;

    // ------------------------------------------------------------------------

    /**
     * Item::__construct
     *
     * @param string            $key          Item key.
     * @param mixed|null        $value        Item value.
     * @param int|\DateInterval $expiresAfter Item expiration.
     *
     * @throws \Exception
     * @return Item
     */
    public function __construct($key, $value = null, $expiresAfter = 300)
    {
        // Set item key
        $this->key = $key;

        // Set from item metadata
        if (isset($value[ 'ctime' ]) AND isset($value[ 'etime' ]) AND isset($value[ 'ttl' ]) AND isset($value[ 'data' ])) {
            $this->set($value[ 'data' ]);
            $this->createdAt = new \DateTime(date('r', $value[ 'ctime' ]));
            $this->expiresAt = new \DateTime(date('r', $value[ 'etime' ]));
            $this->expiresAfter($value[ 'ttl' ]);
        } else {
            // Set item value
            $this->set($value);

            // Set item creation time
            $this->createdAt = new \DateTime(date('r', time()));

            // Set item expiration
            if ($expiresAfter !== false) {
                $this->expiresAfter($expiresAfter);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Item::set
     *
     * Sets the value represented by this cache item.
     *
     * The $value argument may be any item that can be serialized by PHP,
     * although the method of serialization is left up to the Implementing
     * Library.
     *
     * @param mixed $value
     *   The serializable value to be stored.
     *
     * @return static
     *   The invoked object.
     */
    public function set($value)
    {
        $this->value = $value;

        if ( ! is_null($value)) {
            $this->isHit = true;
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Item::expiresAfter
     *
     * Sets the expiration time for this cache item.
     *
     * @param int|\DateInterval|null $time
     *   The period of time from the present after which the item MUST be considered
     *   expired. An integer parameter is understood to be the time in seconds until
     *   expiration. If null is passed explicitly, a default value MAY be used.
     *   If none is set, the value should be stored permanently or for as long as the
     *   implementation allows.
     *
     * @return static
     *   The called object.
     * @throws \Exception
     */
    public function expiresAfter($time = null)
    {
        $time = is_null($time) ? 300 : $time;

        if (is_int($time)) {
            $this->expiresAfter = new \DateInterval('PT' . $time . 'S');
        }

        if ( ! $this->expiresAt instanceof \DateTime) {
            $this->expiresAt();
        }

        $this->expiresAt->add($this->expiresAfter);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Item::expiresAt
     *
     * Sets the expiration time for this cache item.
     *
     * @param \DateTimeInterface|null $expiration
     *   The point in time after which the item MUST be considered expired.
     *   If null is passed explicitly, a default value MAY be used. If none is set,
     *   the value should be stored permanently or for as long as the
     *   implementation allows.
     *
     * @return static
     *   The called object.
     * @throws \Exception
     */
    public function expiresAt($expiration = null)
    {
        $this->expiresAt = isset($expiration) ? $expiration : new \DateTime();

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Item::getKey
     *
     * Returns the key for the current cache item.
     *
     * The key is loaded by the Implementing Library, but should be available to
     * the higher level callers when needed.
     *
     * @return string
     *   The key string for this cache item.
     */
    public function getKey()
    {
        return $this->key;
    }

    // ------------------------------------------------------------------------

    /**
     * Item::get
     *
     * Retrieves the value of the item from the cache associated with this object's key.
     *
     * The value returned must be identical to the value originally stored by set().
     *
     * If isHit() returns false, this method MUST return null. Note that null
     * is a legitimate cached value, so the isHit() method SHOULD be used to
     * differentiate between "null value was found" and "no value was found."
     *
     * @return mixed
     *   The value corresponding to this cache item's key, or null if not found.
     */
    public function get()
    {
        if ($this->isHit()) {
            return $this->value;
        }

        return null;
    }

    // ------------------------------------------------------------------------

    /**
     * Item::isHit
     *
     * Confirms if the cache item lookup resulted in a cache hit.
     *
     * Note: This method MUST NOT have a race condition between calling isHit()
     * and calling get().
     *
     * @return bool
     *   True if the request resulted in a cache hit. False otherwise.
     */
    public function isHit()
    {
        return $this->isHit;
    }

    // ------------------------------------------------------------------------

    /**
     * Item::getMetadata
     *
     * Gets item metadata info in array format:
     * [
     *     'ctime' => 1474609788, // creation time
     *     'etime' => '1474610088', // expires time
     *     'ttl' => 300 // time-to-live
     * ]
     *
     * @return array
     */
    public function getMetadata()
    {
        $createdTime = $this->createdAt->format('U');

        if ($this->expiresAt instanceof \DateTime) {
            $expiresTime = $this->expiresAt->format('U');
            $ttl = $expiresTime - $createdTime;
        } else {
            $expiresTime = 0;
            $ttl = 0;
        }

        return [
            'ctime' => $createdTime,
            'etime' => $expiresTime,
            'ttl'   => $ttl,
        ];
    }
}