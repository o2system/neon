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

use O2System\Cache\Abstracts\AbstractAdapter;

/**
 * Class Adapter
 *
 * @package O2System\Cache\Adapters\Apc
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
    protected $platform = 'APC User Cache (APCu)';

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
        $this->config = $config;

        // APCu adapter do not require further processing.
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
     * @return bool
     */
    public function increment($key, $step = 1)
    {
        $success = false;
        apcu_inc($this->prefixKey . $key, $step, $success);

        return $success;
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
        $success = false;
        apcu_dec($this->prefixKey . $key, $step, $success);

        return $success;
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
        @list($limited) = func_get_args();

        return apcu_cache_info(@$limited);
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
        @list($limited) = func_get_args();

        return apcu_sma_info(@$limited);
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
        return (bool)(extension_loaded('apcu') && ini_get('apc.enabled'));
    }

    /**
     * Adapter::isConnected
     *
     * Checks if this adapter has a successful connection.
     *
     * @return bool Returns FALSE if not supported.
     */
    public function isConnected()
    {
        return (bool)function_exists('apcu_cache_info');
    }
}