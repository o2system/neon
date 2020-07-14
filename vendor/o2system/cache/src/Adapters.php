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

use O2System\Cache\Abstracts\AbstractAdapter;
use O2System\Cache\Abstracts\AbstractItemPool;
use O2System\Spl\Patterns\Structural\Provider\AbstractProvider;
use O2System\Spl\Patterns\Structural\Provider\ValidationInterface;

/**
 * Class Adapters
 *
 * @package O2System\Cache
 */
class Adapters extends AbstractProvider implements ValidationInterface
{
    /**
     * Adapters::__construct
     *
     * @param DataStructures\Config $config
     *
     * @return Adapters
     */
    public function __construct(DataStructures\Config $config)
    {
        if ($config->offsetExists('default')) {
            foreach ($config as $poolOffset => $poolConfig) {
                $this->createItemPool($poolOffset, $poolConfig);
            }
        } elseif ($config->offsetExists('adapter')) {
            $this->createItemPool('default', $config);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Adapters::createItemPool
     *
     * Create Item Pool
     *
     * @param string                $poolOffset
     * @param DataStructures\Config $poolConfig
     */
    public function createItemPool($poolOffset, DataStructures\Config $poolConfig)
    {
        $adapterClassName = '\O2System\Cache\Adapters\\' . ucfirst($poolConfig->adapter) . '\ItemPool';

        if (class_exists($adapterClassName)) {
            $adapter = new $adapterClassName($poolConfig);

            if ($adapter instanceof AbstractAdapter) {
                if ($adapter->isSupported()) {
                    $this->register($adapter, $poolOffset);
                }
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Adapters::getItemPool
     *
     * Gets item pool.
     *
     * @param string $poolOffset
     *
     * @return mixed
     */
    public function &getItemPool($poolOffset)
    {
        return $this->getObject($poolOffset);
    }

    // ------------------------------------------------------------------------

    public function hasItemPool($poolOffset)
    {
        return $this->__isset($poolOffset);
    }

    // ------------------------------------------------------------------------

    /**
     * Adapters::validate
     *
     * Determine if value is meet requirement.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function validate($value)
    {
        if ($value instanceof AbstractItemPool) {
            return true;
        }

        return false;
    }
}