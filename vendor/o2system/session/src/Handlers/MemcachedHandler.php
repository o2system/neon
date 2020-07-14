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

namespace O2System\Session\Handlers;

// ------------------------------------------------------------------------

use O2System\Spl\Exceptions\Logic\BadFunctionCall\BadPhpExtensionCallException;
use Psr\Log\LoggerInterface;
use O2System\Session\Abstracts\AbstractHandler;
use O2System\Session\DataStructures\Config;

/**
 * Class MemcachedHandler
 *
 * @package O2System\Session\Handlers
 */
class MemcachedHandler extends AbstractHandler
{
    /**
     * Platform Name
     *
     * @access  protected
     * @var string
     */
    protected $platform = 'memcached';

    /**
     * Memcached Object
     *
     * @var \Memcache|\Memcached
     */
    protected $memcached;

    // ------------------------------------------------------------------------

    /**
     * MemcachedHandler::__construct
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $config->merge(
            [
                'host'   => '127.0.0.1',
                'port'   => 11211,
                'weight' => 1,
            ]
        );

        if ($this->isSupported() === false) {
            throw new BadPhpExtensionCallException('E_MEMCACHED_EXTENSION');
        }

        parent::__construct($config);
    }

    /**
     * MemcachedHandler::open
     *
     * Initialize session
     *
     * @link  http://php.net/manual/en/sessionhandlerinterface.open.php
     *
     * @param string $save_path The path where to store/retrieve the session.
     * @param string $name      The session name.
     *
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    public function open($save_path, $name)
    {
        if (class_exists('Memcached', false)) {
            $this->memcached = new \Memcached();
            $this->memcached->setOption(\Memcached::OPT_BINARY_PROTOCOL, true); // required for touch() usage
        } else {
            if ($this->logger instanceof LoggerInterface) {
                $this->logger->error('SESSION_E_PLATFORM_UNSUPPORTED', ['Memcache']);
            }

            return false;
        }

        if (isset($this->config[ 'servers' ])) {
            foreach ($this->config[ 'servers' ] as $server => $setup) {
                isset($setup[ 'port' ]) OR $setup[ 'port' ] = 11211;
                isset($setup[ 'weight' ]) OR $setup[ 'weight' ] = 1;

                $this->memcached->addServer(
                    $setup[ 'host' ],
                    $setup[ 'port' ],
                    $setup[ 'weight' ]
                );
            }
        } else {
            $this->memcached->addServer(
                $this->config[ 'host' ],
                $this->config[ 'port' ],
                $this->config[ 'weight' ]
            );
        }

        if ($this->memcached->getVersion() === false) {
            if ($this->logger instanceof LoggerInterface) {
                $this->logger->error('SESSION_E_MEMCACHED_CONNECTION_REFUSED');
            }

            return false;
        }

        return true;
    }

    // ------------------------------------------------------------------------

    /**
     * MemcachedHandler::close
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
    public function close()
    {
        if (isset($this->memcached)) {
            isset($this->lockKey) AND $this->memcached->delete($this->lockKey);

            if ($this->memcached instanceof \Memcached) {
                $this->memcached->quit();
            } elseif ($this->memcached instanceof \MemcachePool) {
                $this->memcached->close();
            }

            $this->memcached = null;

            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * MemcachedHandler::destroy
     *
     * Destroy a session
     *
     * @link  http://php.net/manual/en/sessionhandlerinterface.destroy.php
     *
     * @param string $session_id The session ID being destroyed.
     *
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    public function destroy($session_id)
    {
        if (isset($this->memcached, $this->lockKey)) {
            $this->memcached->delete($this->prefixKey . $session_id);

            return $this->destroyCookie();
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * MemcachedHandler::gc
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
    public function gc($maxlifetime)
    {
        // Not necessary, Memcached takes care of that.
        return true;
    }

    // ------------------------------------------------------------------------

    /**
     * MemcachedHandler::read
     *
     * Read session data
     *
     * @link  http://php.net/manual/en/sessionhandlerinterface.read.php
     *
     * @param string $session_id The session id to read data for.
     *
     * @return string <p>
     * Returns an encoded string of the read data.
     * If nothing was read, it must return an empty string.
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    public function read($session_id)
    {
        if (isset($this->memcached) AND $this->lockSession($session_id)) {
            // Needed by write() to detect session_regenerate_id() calls
            $this->sessionId = $session_id;

            $sessionData = (string)$this->memcached->get($this->prefixKey . $session_id);
            $this->fingerprint = md5($sessionData);

            return $sessionData;
        }

        return '';
    }

    // ------------------------------------------------------------------------

    /**
     * MemcachedHandler::_lockSession
     *
     * Acquires an (emulated) lock.
     *
     * @param   string $session_id Session ID
     *
     * @return  bool
     */
    protected function lockSession($session_id)
    {
        if (isset($this->lockKey)) {
            return $this->memcached->replace($this->lockKey, time(), 300);
        }

        // 30 attempts to obtain a lock, in case another request already has it
        $lockKey = $this->prefixKey . $session_id . ':lock';
        $attempt = 0;

        do {
            if ($this->memcached->get($lockKey)) {
                sleep(1);
                continue;
            }

            if ( ! @$this->memcached->set($lockKey, time(), 300)) {
                if ($this->logger instanceof LoggerInterface) {
                    $this->logger->error('SESSION_E_OBTAIN_LOCK', [$this->prefixKey . $session_id]);
                }

                return false;
            }

            $this->lockKey = $lockKey;
            break;
        } while (++$attempt < 30);

        if ($attempt === 30) {
            if ($this->logger instanceof LoggerInterface) {
                $this->logger->error('SESSION_E_OBTAIN_LOCK_30', [$this->prefixKey . $session_id]);
            }

            return false;
        }

        $this->isLocked = true;

        return true;
    }

    // ------------------------------------------------------------------------

    /**
     * MemcachedHandler::write
     *
     * Write session data
     *
     * @link  http://php.net/manual/en/sessionhandlerinterface.write.php
     *
     * @param string $session_id   The session id.
     * @param string $session_data <p>
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
    public function write($session_id, $session_data)
    {
        if ( ! isset($this->memcached)) {
            return false;
        } // Was the ID regenerated?
        elseif ($session_id !== $this->sessionId) {
            if ( ! $this->lockRelease() OR ! $this->lockSession($session_id)) {
                return false;
            }

            $this->fingerprint = md5('');
            $this->sessionId = $session_id;
        }

        if (isset($this->lockKey)) {
            $this->memcached->replace($this->lockKey, time(), 300);

            if ($this->fingerprint !== ($fingerprint = md5($session_data))) {
                if ($this->memcached->set(
                    $this->prefixKey . $session_id,
                    $session_data,
                    $this->config[ 'lifetime' ]
                )
                ) {
                    $this->fingerprint = $fingerprint;

                    return true;
                }

                return false;
            }

            return $this->memcached->touch($this->prefixKey . $session_id, $this->config[ 'lifetime' ]);
        }

        return false;
    }

    //--------------------------------------------------------------------

    /**
     * MemcachedHandler::_lockRelease
     *
     * Releases a previously acquired lock
     *
     * @return    bool
     */
    protected function lockRelease()
    {
        if (isset($this->memcached, $this->lockKey) AND $this->isLocked) {
            if ( ! $this->memcached->delete($this->lockKey) AND
                $this->memcached->getResultCode() !== \Memcached::RES_NOTFOUND
            ) {
                if ($this->logger instanceof LoggerInterface) {
                    $this->logger->error('SESSION_E_FREE_LOCK', [$this->lockKey]);
                }

                return false;
            }

            $this->lockKey = null;
            $this->isLocked = false;
        }

        return true;
    }

    //--------------------------------------------------------------------

    /**
     * MemcachedHandler::isSupported
     *
     * Checks if this platform is supported on this system.
     *
     * @return bool Returns FALSE if unsupported.
     */
    public function isSupported()
    {
        return (bool)extension_loaded('memcached');
    }
}