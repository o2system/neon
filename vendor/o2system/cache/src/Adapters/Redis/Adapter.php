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

use O2System\Cache\Abstracts\AbstractAdapter;
use O2System\Spl\Exceptions\Logic\DomainException;
use O2System\Spl\Exceptions\Logic\InvalidArgumentException;
use O2System\Spl\Exceptions\RuntimeException;

/**
 * Class Adapter
 *
 * @package O2System\Cache\Adapters\File
 */
abstract class Adapter extends AbstractAdapter
{
    /**
     * Adapter::$platform
     *
     * Adapter Platform Name
     *
     * @var string
     */
    protected $platform = 'Redis';

    /**
     * Adapter::$redis
     *
     * Redis Instance
     *
     * @var \Redis
     */
    protected $redis;

    // ------------------------------------------------------------------------

    /**
     * Adapter::connect
     *
     * @param array $config Cache adapter connection configuration.
     *
     * @return void
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    public function connect(array $config)
    {
        $this->config = array_merge(
            [
                'host'     => '127.0.0.1',
                'port'     => 6379,
                'password' => null,
                'timeout'  => 0,
            ],
            $config
        );

        $this->redis = new \Redis();

        try {
            if ( ! $this->redis->connect(
                $this->config[ 'host' ],
                ($this->config[ 'host' ][ 0 ] === '/' ? 0
                    : $this->config[ 'port' ]),
                $this->config[ 'timeout' ]
            )
            ) {
                throw new RuntimeException('CACHE_REDIS_E_CONNECTION_FAILED');
            }

            if (isset($this->config[ 'password' ]) AND ! $this->redis->auth($this->config[ 'password' ])) {
                throw new DomainException('CACHE_REDIS_E_AUTHENTICATION_FAILED');
            }

            if (isset($this->config[ 'dbIndex' ]) AND ! $this->redis->select($this->config[ 'dbIndex' ])) {
                throw new RuntimeException('CACHE_REDIS_E_DB_CONNECTION_FAILED');
            }
        } catch (\RedisException $e) {
            throw new RuntimeException('E_REDIS_ADAPTER_CONNECTION_REFUSED', $e->getCode(), [$e->getMessage()]);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Adapter::increment
     *
     * Increment a raw value offset.
     *
     * @param string $key  Cache item key.
     * @param int    $step Increment step to add.
     *
     * @throws \O2System\Spl\Exceptions\Logic\InvalidArgumentException
     *   If any of the keys in $keys are not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return mixed New value on success or FALSE on failure.
     */
    public function increment($key, $step = 1)
    {
        if ( ! is_string($key)) {
            throw new InvalidArgumentException('E_INVALID_ARGUMENT_STRING_CACHE_EXCEPTION');
        }

        return $this->redis->hIncrBy($this->prefixKey . $key, 'data', $step);
    }

    // ------------------------------------------------------------------------

    /**
     * Adapter::decrement
     *
     * Decrement a raw value offset.
     *
     * @param string $key  Cache item key.
     * @param int    $step Decrement step to add.
     *
     * @throws \O2System\Spl\Exceptions\Logic\InvalidArgumentException
     *   If any of the keys in $keys are not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return mixed New value on success or FALSE on failure.
     */
    public function decrement($key, $step = 1)
    {
        if ( ! is_string($key)) {
            throw new InvalidArgumentException('E_INVALID_ARGUMENT_STRING_CACHE_EXCEPTION');
        }

        return $this->redis->hIncrBy($this->prefixKey . $key, 'data', -$step);
    }

    // ------------------------------------------------------------------------

    /**
     * Adapter::getInfo
     *
     * Gets item pool adapter info.
     *
     * @return mixed
     */
    public function getInfo()
    {
        return call_user_func_array([&$this->redis, 'info'], func_get_args());
    }

    // ------------------------------------------------------------------------

    /**
     * Adapter::getInfo
     *
     * Gets item pool adapter stats.
     *
     * @return mixed
     */
    public function getStats()
    {
        return $this->redis->info('stats');
    }

    // ------------------------------------------------------------------------

    /**
     * Adapter::isSupported
     *
     * Checks if this adapter is supported on this system.
     *
     * @return bool Returns FALSE if not supported.
     */
    public function isSupported()
    {
        return (bool)(extension_loaded('redis') && class_exists('Redis', false));
    }

    // ------------------------------------------------------------------------

    /**
     * Adapter::isConnected
     *
     * Checks if this adapter has a successful connection.
     *
     * @return bool Returns FALSE if not supported.
     */
    public function isConnected()
    {
        return (bool)($this->redis instanceof \Redis);
    }
}