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

namespace O2System\Email\Protocols\Abstracts;

// ------------------------------------------------------------------------

use O2System\Email\Message;
use O2System\Email\Spool;
use O2System\Spl\Traits\Collectors\ErrorCollectorTrait;

/**
 * Class AbstractProtocol
 *
 * @package O2System\Email\Abstracts
 */
abstract class AbstractProtocol
{
    use ErrorCollectorTrait;

    /**
     * AbstractProtocol::$spool
     *
     * @var Spool
     */
    protected $spool;

    /**
     * AbstractProtocol::$message
     *
     * @var Message
     */
    protected $message;

    // ------------------------------------------------------------------------

    /**
     * AbstractProtocol constructor.
     *
     * @param \O2System\Email\Spool $spool
     */
    public function __construct(Spool $spool)
    {
        $this->spool = $spool;
    }

    /**
     * AbstractProtocol::send
     *
     * Send the message.
     *
     * @param Message $message
     *
     * @return bool
     */
    public function send(Message $message)
    {
        return $this->sending($message);
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractProtocol::sending
     *
     * Protocol message sending process.
     *
     * @param Message $message
     *
     * @return bool
     */
    abstract protected function sending(Message $message);
}