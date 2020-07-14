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

namespace O2System\Database;

// ------------------------------------------------------------------------

use O2System\Spl\Patterns\Structural\Provider\AbstractProvider;
use O2System\Spl\Patterns\Structural\Provider\ValidationInterface;

/**
 * Class Connections
 *
 * @package O2System\Database
 */
class Connections extends AbstractProvider implements ValidationInterface
{
    /**
     * Connections::$config
     *
     * @var DataStructures\Config
     */
    private $config;

    /**
     * Connections::__construct
     *
     * @param DataStructures\Config $config
     *
     * @return Connections
     */
    public function __construct(DataStructures\Config $config)
    {
        $this->config = $config;
    }

    // ------------------------------------------------------------------------

    public function &loadConnection($connectionOffset)
    {
        $loadConnection[ $connectionOffset ] = false;

        if ( ! $this->exists($connectionOffset) and $this->config->offsetExists($connectionOffset)) {

            $connectionConfig = $this->config->offsetGet($connectionOffset);

            if (is_array($connectionConfig)) {
                new DataStructures\Config($this->config[ $connectionOffset ]);
            }

            $this->createConnection($connectionOffset, $connectionConfig);

            return $this->getObject($connectionOffset);

        } elseif ($this->exists($connectionOffset)) {
            return $this->getObject($connectionOffset);
        }

        return $loadConnection;
    }

    // ------------------------------------------------------------------------

    /**
     * Connections::createConnection
     *
     * Create Item Pool
     *
     * @param string                $connectionOffset
     * @param DataStructures\Config $connectionConfig
     *
     * @return bool|\O2System\Database\Sql\Abstracts\AbstractConnection|\O2System\Database\NoSql\Abstracts\AbstractConnection
     */
    public function &createConnection($connectionOffset, DataStructures\Config $connectionConfig)
    {
        $driverMaps = [
            'mongodb' => '\O2System\Database\NoSql\Drivers\MongoDb\Connection',
            'mysql'   => '\O2System\Database\Sql\Drivers\MySql\Connection',
            'sqlite'  => '\O2System\Database\Sql\Drivers\Sqlite\Connection',
        ];

        if (array_key_exists($connectionConfig->driver, $driverMaps)) {
            if (class_exists($driverClassName = $driverMaps[ $connectionConfig->driver ])) {
                $driverInstance = new $driverClassName($connectionConfig);
                $this->register($driverInstance, $connectionOffset);
            }

            return $this->getObject($connectionOffset);
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Connections::validate
     *
     * Determine if value is meet requirement.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function validate($value)
    {
        if ($value instanceof \O2System\Database\Sql\Abstracts\AbstractConnection || $value instanceof \O2System\Database\NoSql\Abstracts\AbstractConnection) {
            return true;
        }

        return false;
    }
}