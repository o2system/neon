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

namespace O2System\Email;

// ------------------------------------------------------------------------

use O2System\Email\DataStructures\Config;
use O2System\Email\Protocols\Abstracts\AbstractProtocol;
use O2System\Spl\Traits\Collectors\ErrorCollectorTrait;

/**
 * Class Spool
 *
 * @package O2System\Email
 */
class Spool
{
    use ErrorCollectorTrait;

    /**
     * Spool::$config
     *
     * Spool configuration.
     *
     * @var Config
     */
    protected $config;

    /**
     * Spool::$log
     *
     * Spool log.
     *
     * @var array
     */
    protected $log = [];

    // ------------------------------------------------------------------------

    /**
     * Spool::__construct
     *
     * @param Config|null $config
     */
    public function __construct(Config $config = null)
    {
        if (empty($config)) {
            $config = new Config();
        }

        $this->setConfig($config);
    }

    // ------------------------------------------------------------------------

    /**
     * Spool::getConfig
     *
     * Gets spool configurations.
     *
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    // ------------------------------------------------------------------------

    /**
     * Spool::setConfig
     *
     * Sets spool config.
     *
     * @param Config $config
     *
     * @return static
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Spool::addLog
     *
     * Add spool log.
     *
     * @param $log
     */
    public function addLog($log)
    {
        $this->log[] = $log;
    }

    // ------------------------------------------------------------------------

    /**
     * Spool::getLog
     *
     * Gets spool log.
     *
     * @return array
     */
    public function getLog()
    {
        return $this->log;
    }

    // ------------------------------------------------------------------------

    /**
     * Spool::send
     *
     * @param \O2System\Email\Message $message
     *
     * @return bool
     */
    public function send(Message $message)
    {
        $protocolClass = '\O2System\Email\Protocols\\' . ucfirst($this->config->offsetGet('protocol')) . 'Protocol';

        if (class_exists($protocolClass)) {
            $protocol = new $protocolClass($this);

            if ($protocol instanceof AbstractProtocol) {
                return $protocol->send($message);
            }
        }

        return false;
    }
}