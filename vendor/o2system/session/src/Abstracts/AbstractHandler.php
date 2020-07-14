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

namespace O2System\Session\Abstracts;

// ------------------------------------------------------------------------

use O2System\Kernel\DataStructures\Config;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractHandler
 *
 * Base class of session platform handlers.
 *
 * @package O2System\Session\Handler
 */
abstract class AbstractHandler implements \SessionHandlerInterface, LoggerAwareInterface
{
    /**
     * Session Handler Platform Name
     *
     * @var string
     */
    protected $platform;

    /**
     * Session Handler Config
     *
     * @var Config
     */
    protected $config;

    /**
     * Session Cache Key Prefix
     *
     * @var string
     */
    protected $prefixKey = 'o2session:';

    /**
     * Session Lock Key
     *
     * @var string
     */
    protected $lockKey;

    /**
     * Session Data Fingerprint
     *
     * @var bool
     */
    protected $fingerprint;

    /**
     * Session Is Locked Flag
     *
     * @var bool
     */
    protected $isLocked = false;

    /**
     * Current session ID
     *
     * @var string
     */
    protected $sessionId;

    /**
     * Success and failure return values
     *
     * Necessary due to a bug in all PHP 5 versions where return values
     * from userspace handlers are not handled properly. PHP 7 fixes the
     * bug, so we need to return different values depending on the version.
     *
     * @see    https://wiki.php.net/rfc/session.user.return-value
     * @var    mixed
     */
    protected $success, $failure;

    /**
     * Logger Instance
     *
     * @var LoggerInterface
     */
    protected $logger;

    //--------------------------------------------------------------------

    /**
     * AbstractHandler::__construct
     *
     * @param \O2System\Kernel\DataStructures\Config $config
     *
     * @return AbstractHandler
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->config->offsetUnset('handler');
        $this->setPrefixKey($this->config[ 'name' ]);

        if (is_php('7')) {
            $this->success = true;
            $this->failure = false;
        } else {
            $this->success = 0;
            $this->failure = -1;
        }
    }

    //--------------------------------------------------------------------

    /**
     * AbstractHandler::setPrefixKey
     *
     * Sets cache prefix key
     *
     * @param $prefixKey
     */
    public function setPrefixKey($prefixKey)
    {
        $this->prefixKey = rtrim($prefixKey, ':') . ':';
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractHandler::setLogger
     *
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger =& $logger;
    }

    /**
     * AbstractHandler::getPlatform
     *
     * Get Current Platform
     *
     * @return string
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractHandler::open
     *
     * Initialize session
     *
     * @link  http://php.net/manual/en/sessionhandlerinterface.open.php
     *
     * @param string $savePath The path where to store/retrieve the session.
     * @param string $name     The session name.
     *
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    abstract public function open($savePath, $name);

    /**
     * AbstractHandler::close
     *
     * Close the session
     *
     * @link  http://php.net/manual/en/sessionhandlerinterface.close.php
     * @return bool <p>
     *        The return value (usually TRUE on success, FALSE on failure).
     *        Note this value is returned internally to PHP for processing.
     *        </p>
     * @since 5.4.0
     */
    abstract public function close();

    /**
     * AbstractHandler::destroy
     *
     * Destroy a session
     *
     * @link  http://php.net/manual/en/sessionhandlerinterface.destroy.php
     *
     * @param string $sessionId The session ID being destroyed.
     *
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    abstract public function destroy($sessionId);

    /**
     * AbstractHandler::gc
     *
     * Cleanup old sessions
     *
     * @link  http://php.net/manual/en/sessionhandlerinterface.gc.php
     *
     * @param int $maxlifetime <p>
     *                         Sessions that have not updated for
     *                         the last maxlifetime seconds will be removed.
     *                         </p>
     *
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    abstract public function gc($maxlifetime);

    /**
     * AbstractHandler::read
     *
     * Read session data
     *
     * @link  http://php.net/manual/en/sessionhandlerinterface.read.php
     *
     * @param string $sessionId The session id to read data for.
     *
     * @return string <p>
     * Returns an encoded string of the read data.
     * If nothing was read, it must return an empty string.
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    abstract public function read($sessionId);

    /**
     * AbstractHandler::write
     *
     * Write session data
     *
     * @link  http://php.net/manual/en/sessionhandlerinterface.write.php
     *
     * @param string $sessionId    The session id.
     * @param string $sessionData  <p>
     *                             The encoded session data. This data is the
     *                             result of the PHP internally encoding
     *                             the $_SESSION superglobal to a serialized
     *                             string and passing it as this parameter.
     *                             Please note sessions use an alternative serialization method.
     *                             </p>
     *
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    abstract public function write($sessionId, $sessionData);

    /**
     * AbstractHandler::isSupported
     *
     * Checks if this platform is supported on this system.
     *
     * @return bool Returns FALSE if not supported.
     */
    abstract public function isSupported();

    //--------------------------------------------------------------------

    /**
     * AbstractHandler::_lockSession
     *
     * A dummy method allowing drivers with no locking functionality
     * (databases other than PostgreSQL and MySQL) to act as if they
     * do acquire a lock.
     *
     * @param string $sessionId
     *
     * @return bool
     */
    protected function lockSession($sessionId)
    {
        $this->isLocked = true;

        return true;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractHandler::_lockRelease
     *
     * Releases the lock, if any.
     *
     * @return bool
     */
    protected function lockRelease()
    {
        $this->isLocked = false;

        return true;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractHandler::_destroyCookie
     *
     * Internal method to force removal of a cookie by the client
     * when session_destroy() is called.
     *
     * @return bool
     */
    protected function destroyCookie()
    {
        return setcookie(
            $this->config[ 'name' ],
            null,
            1,
            $this->config[ 'cookie' ]->path,
            '.' . ltrim($this->config[ 'cookie' ]->domain, '.'),
            $this->config[ 'cookie' ]->secure,
            true
        );
    }
}