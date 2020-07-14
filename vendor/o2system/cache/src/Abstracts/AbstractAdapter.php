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

use O2System\Cache\DataStructures\Config;
use O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException;

/**
 * Class AbstractAdapter
 *
 * @package O2System\Cache\Abstracts
 */
abstract class AbstractAdapter extends AbstractItemPool
{
    /**
     * AbstractAdapter::$platform
     *
     * Adapter Platform Name
     *
     * @var string
     */
    protected $platform;

    /**
     * Adapter Config
     *
     * @var array
     */
    protected $config = [];

    /**
     * AbstractAdapter::$config
     *
     * Adapter Prefix Key
     *
     * @var string
     */
    protected $prefixKey;

    // ------------------------------------------------------------------------

    /**
     * AbstractAdapter::__construct
     *
     * @param \O2System\Cache\DataStructures\Config|NULL $config
     *
     * @return AbstractAdapter
     * @throws BadDependencyCallException
     */
    public function __construct(Config $config = null)
    {
        language()
            ->addFilePath(str_replace('Abstracts', '', __DIR__) . DIRECTORY_SEPARATOR)
            ->loadFile('cache');

        if (isset($config)) {
            if ($this->isSupported()) {
                $this->connect($config->getArrayCopy());

                if ($config->offsetExists('prefixKey')) {
                    $this->setPrefixKey($config->prefixKey);
                }
            } else {
                throw new BadDependencyCallException('E_UNSUPPORTED_ADAPTER_CACHE_EXCEPTION', 0, [$this->platform]);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractAdapter::isSupported
     *
     * Checks if this adapter is supported on this system.
     *
     * @return bool Returns FALSE if not supported.
     */
    abstract public function isSupported();

    // ------------------------------------------------------------------------

    /**
     * AbstractAdapter::setPrefixKey
     *
     * Sets item prefix key.
     *
     * @param $prefixKey
     */
    public function setPrefixKey($prefixKey)
    {
        $this->prefixKey = rtrim($prefixKey, ':') . ':';
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractAdapter::getPlatform
     *
     * Gets item pool adapter platform name.
     *
     * @return string
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractAdapter::isConnected
     *
     * Checks if this adapter has a successful connection.
     *
     * @return bool Returns FALSE if not supported.
     */
    abstract public function isConnected();
}