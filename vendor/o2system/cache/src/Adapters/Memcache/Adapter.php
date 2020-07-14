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

namespace O2System\Cache\Adapters\Memcache;

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
    protected $platform = 'Memcache';

    // ------------------------------------------------------------------------

    /**
     * Adapter::$memchace
     *
     * Memcache Instance
     *
     * @var \Memcache
     */
    protected $memcache;

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
        return (bool)(extension_loaded('memcache') && class_exists('Memcache', false));
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
        $this->memcache = new \Memcache();

        if (empty($config)) {
            $this->config = [
                'host'          => '127.0.0.1',
                'port'          => 11211,
                'persistent'    => true,
                'timeout'       => 1,
                'retryInterval' => 15,
                'weight'        => 1,
            ];

            $this->memcache->addserver($this->config[ 'host' ], $this->config[ 'port' ]);
        } elseif (isset($config[ 'servers' ])) {
            foreach ($config[ 'servers' ] as $server) {
                $this->config[ $server[ 'host' ] ] = array_merge(
                    [
                        'host'       => '127.0.0.1',
                        'port'       => 11211,
                        'persistent' => true,
                        'timeout'    => 1,
                        'weight'     => 1,
                    ],
                    $server
                );

                if (array_key_exists('status', $server)) {
                    if ($server[ 'status' ] === false) {
                        $this->config[ $server[ 'host' ] ][ 'retryInterval' ] = -1;

                        $this->memcache->addserver(
                            $this->config[ $server[ 'host' ] ][ 'host' ],
                            $this->config[ $server[ 'host' ] ][ 'port' ],
                            $this->config[ $server[ 'host' ] ][ 'persistent' ],
                            $this->config[ $server[ 'host' ] ][ 'weight' ],
                            $this->config[ $server[ 'host' ] ][ 'timeout' ],
                            $this->config[ $server[ 'host' ] ][ 'retryInterval' ],
                            false
                        );

                        continue;
                    }
                }

                $this->memcache->addserver(
                    $this->config[ $server[ 'host' ] ][ 'host' ],
                    $this->config[ $server[ 'host' ] ][ 'port' ],
                    $this->config[ $server[ 'host' ] ][ 'persistent' ],
                    $this->config[ $server[ 'host' ] ][ 'weight' ],
                    $this->config[ $server[ 'host' ] ][ 'timeout' ]
                );
            }
        } else {
            $this->config = array_merge(
                [
                    'host'       => '127.0.0.1',
                    'port'       => 11211,
                    'persistent' => true,
                    'timeout'    => 1,
                    'weight'     => 1,
                ],
                $config
            );

            if (isset($this->config[ 'status' ])) {
                if ($this->config[ 'status' ] === false) {
                    $this->memcache->addserver(
                        $this->config[ 'host' ],
                        $this->config[ 'port' ],
                        $this->config[ 'persistent' ],
                        $this->config[ 'weight' ],
                        $this->config[ 'timeout' ],
                        -1,
                        false
                    );
                } else {
                    $this->memcache->addserver(
                        $this->config[ 'host' ],
                        $this->config[ 'port' ],
                        $this->config[ 'persistent' ],
                        $this->config[ 'weight' ],
                        $this->config[ 'timeout' ]
                    );
                }
            } else {
                $this->memcache->addserver(
                    $this->config[ 'host' ],
                    $this->config[ 'port' ],
                    $this->config[ 'persistent' ],
                    $this->config[ 'weight' ],
                    $this->config[ 'timeout' ]
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
        return (bool)($this->memcache instanceof \Memcache);
    }

    // ------------------------------------------------------------------------

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
        } elseif ($this->memcache instanceof \Memcache AND method_exists($this->memcache, $method)) {
            return call_user_func_array([&$this->memcache, $method], $arguments);
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
        return $this->memcache->increment($this->prefixKey . $key, $step);
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
        return $this->memcache->decrement($this->prefixKey . $key, $step);
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
            'version' => $this->memcache->getVersion(),
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
        return $this->memcache->getExtendedStats();
    }
}