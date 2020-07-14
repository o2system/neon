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

namespace O2System\Cache\Adapters\Memcached;

// ------------------------------------------------------------------------

use O2System\Cache\Abstracts\AbstractAdapter;
use O2System\Cache\DataStructures\Config;
use O2System\Spl\Exceptions\Logic\BadFunctionCall\BadMethodCallException;

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
    protected $platform = 'Memcached';

    // ------------------------------------------------------------------------

    /**
     * Adapter::$memchached
     *
     * Memcached Instance
     *
     * @var \Memcached
     */
    protected $memcached;

    // ------------------------------------------------------------------------

    /**
     * AbstractAdapter::__construct
     *
     * @param Config|NULL $config
     *
     * @return Adapter
     */
    public function __construct(Config $config = null)
    {
        if (isset($config)) {
            if ($this->isSupported()) {
                $this->connect($config->getArrayCopy());

                if ($config->offsetExists('prefixKey')) {
                    $this->setPrefixKey($config->prefixKey);
                }
            }
        } elseif ($this->isSupported()) {
            $this->connect([]);
        }
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
        return (bool)(extension_loaded('memcached') && class_exists('Memcached', false));
    }

    // ------------------------------------------------------------------------

    /**
     * Adapter::connect
     *
     * @param array $config Cache adapter connection configuration.
     *
     * @return void
     */
    public function connect(array $config)
    {
        $this->memcached = new \Memcached();
        $this->memcached->setOption(\Memcached::OPT_BINARY_PROTOCOL, false);

        if (empty($config)) {
            $this->config = [
                'host'   => '127.0.0.1',
                'port'   => 11211,
                'weight' => 1,
            ];

            $this->memcached->addserver($this->config[ 'host' ], $this->config[ 'port' ]);
        } elseif (isset($config[ 'servers' ])) {
            foreach ($config[ 'servers' ] as $server) {
                $this->config[ $server[ 'host' ] ] = array_merge(
                    [
                        'host'   => '127.0.0.1',
                        'port'   => 11211,
                        'weight' => 1,
                    ],
                    $server
                );

                if (array_key_exists('status', $server)) {
                    if ($server[ 'status' ] === false) {
                        $this->config[ $server[ 'host' ] ][ 'retryInterval' ] = -1;

                        $this->memcached->addserver(
                            $this->config[ $server[ 'host' ] ][ 'host' ],
                            $this->config[ $server[ 'host' ] ][ 'port' ],
                            $this->config[ $server[ 'host' ] ][ 'weight' ]
                        );

                        continue;
                    }
                }

                $this->memcached->addserver(
                    $this->config[ $server[ 'host' ] ][ 'host' ],
                    $this->config[ $server[ 'host' ] ][ 'port' ],
                    $this->config[ $server[ 'host' ] ][ 'weight' ]
                );
            }
        } else {
            $this->config = array_merge(
                [
                    'host'   => '127.0.0.1',
                    'port'   => 11211,
                    'weight' => 1,
                ],
                $config
            );

            if (isset($this->config[ 'status' ])) {
                if ($this->config[ 'status' ] === false) {
                    $this->memcached->addserver(
                        $this->config[ 'host' ],
                        $this->config[ 'port' ],
                        $this->config[ 'weight' ]
                    );
                } else {
                    $this->memcached->addserver(
                        $this->config[ 'host' ],
                        $this->config[ 'port' ],
                        $this->config[ 'weight' ]
                    );
                }
            } else {
                $this->memcached->addserver(
                    $this->config[ 'host' ],
                    $this->config[ 'port' ],
                    $this->config[ 'weight' ]
                );
            }
        }
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
        return (bool)($this->memcached instanceof \Memcached);
    }

    /**
     * Adapter::__call
     *
     * @param string $method    Memcache Adapter Class / Memcache Class / Memcache ItemPool Class method name.
     * @param array  $arguments Method arguments.
     *
     * @return mixed
     * @throws \O2System\Spl\Exceptions\Logic\BadFunctionCall\BadMethodCallException
     */
    public function __call($method, array $arguments = [])
    {
        if (method_exists($this, $method)) {
            return call_user_func_array([&$this, $method], $arguments);
        } elseif ($this->memcached instanceof \Memcache AND method_exists($this->memcached, $method)) {
            return call_user_func_array([&$this->memcached, $method], $arguments);
        }

        throw new BadMethodCallException('E_BAD_METHOD_CALL_CACHE_EXCEPTION', 0, [__CLASS__ . '::' . $method]);
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
     * @return mixed New value on success or FALSE on failure.
     */
    public function increment($key, $step = 1)
    {
        return $this->memcached->increment($this->prefixKey . $key, $step);
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
     * @return mixed New value on success or FALSE on failure.
     */
    public function decrement($key, $step = 1)
    {
        return $this->memcached->decrement($this->prefixKey . $key, $step);
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
        return [
            'version' => $this->memcached->getVersion(),
            'servers' => $this->memcached->getServerList(),
        ];
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
        return $this->memcached->getStats();
    }
}