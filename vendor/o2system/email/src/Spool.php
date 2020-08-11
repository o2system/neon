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
use O2System\Spl\Traits\Collectors\ConfigCollectorTrait;
use O2System\Spl\Traits\Collectors\ErrorCollectorTrait;

/**
 * Class Spool
 *
 * @package O2System\Email
 */
class Spool
{
    use ConfigCollectorTrait;
    use ErrorCollectorTrait;

    /**
     * Spool::$config
     *
     * Spool configuration.
     *
     * @var Config
     */
    protected $config;
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

        $this->setConfig($config->getArrayCopy());
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
                if($protocol->send($message)) {
                    return true;
                }

                $this->setErrors($protocol->getErrors());
            }
        }

        return false;
    }
}