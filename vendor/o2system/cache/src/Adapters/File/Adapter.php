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

use O2System\Cache\Abstracts\AbstractAdapter;
use O2System\Cache\DataStructures\Config;
use O2System\Spl\Exceptions\Logic\InvalidArgumentException;
use O2System\Spl\Exceptions\Runtime\OverflowException;
use O2System\Spl\Info\SplDirectoryInfo;

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
    protected $platform = 'Filesystem Cache';

    /**
     * Adapter::$path
     *
     * Adapter Temporary Path
     *
     * @var string
     */
    protected $path;

    // ------------------------------------------------------------------------

    /**
     * Adapter::__construct
     *
     * @param \O2System\Cache\DataStructures\Config|NULL $config
     *
     * @throws \O2System\Spl\Exceptions\Runtime\OverflowException
     */
    public function __construct(Config $config = null)
    {
        if (isset($config)) {
            $config = $config->getArrayCopy();
        } elseif (is_null($config)) {
            $config = [];
        }

        $this->connect($config);
    }

    /**
     * Adapter::connect
     *
     * @param array $config Cache adapter connection configuration.
     *
     * @return void
     * @throws OverflowException
     */
    public function connect(array $config)
    {
        if (isset($config[ 'path' ])) {
            $config[ 'path' ] = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $config[ 'path' ]);

            if (is_dir($config[ 'path' ])) {
                $this->path = $config[ 'path' ];
            } elseif (defined('PATH_CACHE')) {
                if (is_dir($config[ 'path' ])) {
                    $this->path = $config[ 'path' ];
                } else {
                    $this->path = PATH_CACHE . str_replace(PATH_CACHE, '', $config[ 'path' ]);
                }
            } else {
                $this->path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $config[ 'path' ];
            }
        } elseif (defined('PATH_CACHE')) {
            $this->path = PATH_CACHE;
        } else {
            $this->path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . implode(
                    DIRECTORY_SEPARATOR,
                    ['o2system', 'cache']
                );
        }

        $this->path = rtrim($this->path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if ($this->isSupported() === false) {
            throw new OverflowException('CACHE_FILE_E_UNABLE_TO_WRITE', 0, [$this->path]);
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
        if ( ! is_writable($this->path)) {
            if ( ! file_exists($this->path)) {
                mkdir($this->path, 0777, true);
            }
        }

        return (bool)is_writable($this->path);
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
        return (bool)is_writable($this->path);
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
     * @throws \O2System\Spl\Exceptions\Logic\InvalidArgumentException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function increment($key, $step = 1)
    {
        if ( ! is_string($key)) {
            throw new InvalidArgumentException('E_INVALID_ARGUMENT_STRING_CACHE_EXCEPTION');
        }

        if ($this->hasItem($key)) {
            $item = $this->getItem($key);
            $value = $item->get();

            if (is_int($value)) {
                $value += $step;
                $item->set($value);

                $this->save($item);

                return $value;
            }
        }

        return false;
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
     * @throws \O2System\Spl\Exceptions\Logic\InvalidArgumentException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function decrement($key, $step = 1)
    {
        if ( ! is_string($key)) {
            throw new InvalidArgumentException('E_INVALID_ARGUMENT_STRING_CACHE_EXCEPTION');
        }

        if ($this->hasItem($key)) {
            $item = $this->getItem($key);
            $value = $item->get();

            if (is_int($value)) {
                $value -= $step;
                $item->set($value);

                $this->save($item);

                return $value;
            }
        }

        return false;
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
        return new SplDirectoryInfo($this->path);
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
        $directory = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->path),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $cacheIterator = new \RegexIterator($directory, '/^.+\.cache/i', \RecursiveRegexIterator::GET_MATCH);

        $stats[ 'path' ] = $this->path;
        $stats[ 'files' ] = 0;
        $stats[ 'size' ] = 0;

        foreach ($cacheIterator as $cacheFiles) {
            foreach ($cacheFiles as $cacheFile) {
                $stats[ 'files' ]++;
                $stats[ 'size' ] += filesize($cacheFile);
            }
        }

        return $stats;
    }
}