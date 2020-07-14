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
 * Class RedisHandler
 *
 * @package O2System\Session\Handlers
 */
class RedisHandler extends AbstractHandler
{
    /**
     * Platform Name
     *
     * @access  protected
     * @var string
     */
    protected $platform = 'redis';

    /**
     * Redis Object
     *
     * @var \Redis
     */
    protected $redis;

    // ------------------------------------------------------------------------

    /**
     * RedisHandler::__construct
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $config->merge(
            [
                'socket'   => 'tcp', // 'tcp' or 'unix'
                'host'     => 'localhost', // '103.219.249.198', //
                'port'     => 6379, // 17883, //
                'password' => null, // (optional)
                'timeout'  => 5,
            ]
        );

        if ($this->isSupported() === false) {
            throw new BadPhpExtensionCallException('E_REDIS_EXTENSION');
        }

        parent::__construct($config);
    }

    /**
     * RedisHandler::open
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
        if (class_exists('Redis', false)) {
            $this->redis = new \Redis();
        } else {
            if ($this->logger instanceof LoggerInterface) {
                $this->logger->error('SESSION_E_PLATFORM_UNSUPPORTED', ['Redis']);
            }

            return $this->failure;
        }

        try {
            if ( ! $this->redis->connect(
                $this->config[ 'host' ],
                ($this->config[ 'host' ][ 0 ] === '/' ? 0
                    : $this->config[ 'port' ]),
                $this->config[ 'timeout' ]
            )
            ) {
                if ($this->logger instanceof LoggerInterface) {
                    $this->logger->error('SESSION_E_REDIS_CONNECTION_FAILED', ['Redis']);
                }

                return $this->failure;
            }

            if (isset($this->config[ 'password' ]) AND ! $this->redis->auth($this->config[ 'password' ])) {
                if ($this->logger instanceof LoggerInterface) {
                    $this->logger->error('SESSION_E_REDIS_AUTHENTICATION_FAILED', ['Redis']);
                }

                return $this->failure;
            }

            return true;
        } catch (\RedisException $e) {
            if ($this->logger instanceof LoggerInterface) {
                $this->logger->error('SESSION_E_REDIS_CONNECTION_REFUSED', $e->getMessage());
            }

            return false;
        }
    }

    // ------------------------------------------------------------------------

    /**
     * RedisHandler::close
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
        if (isset($this->redis)) {
            try {
                if ($this->redis->ping() === '+PONG') {
                    isset($this->lockKey) AND $this->redis->delete($this->lockKey);

                    if ( ! $this->redis->close()) {
                        return false;
                    }
                }
            } catch (\RedisException $e) {
                if ($this->logger instanceof LoggerInterface) {
                    $this->logger->error('SESSION_E_REDIS_ON_CLOSE', $e->getMessage());
                }
            }

            $this->redis = null;

            return true;
        }

        return true;
    }

    // ------------------------------------------------------------------------

    /**
     * RedisHandler::destroy
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
        if (isset($this->redis, $this->isLocked)) {
            if (($result = $this->redis->delete($this->prefixKey . $session_id)) !== 1) {
                if ($this->logger instanceof LoggerInterface) {
                    $this->logger->error('SESSION_E_REDIS_ON_DELETE', var_export($result, true));
                }
            }

            return $this->destroyCookie();
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * RedisHandler::gc
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
        // Not necessary, Redis takes care of that.
        return true;
    }

    /**
     * RedisHandler::read
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
        if (isset($this->redis) AND $this->lockSession($session_id)) {
            // Needed by write() to detect session_regenerate_id() calls
            $this->sessionId = $session_id;

            $sessionData = (string)$this->redis->get($this->prefixKey . $session_id);
            $this->fingerprint = md5($sessionData);

            return $sessionData;
        }

        return '';
    }

    // ------------------------------------------------------------------------

    /**
     * RedisHandler::_lockSession
     *
     * Acquires an (emulated) lock.
     *
     * @param    string $session_id Session ID
     *
     * @return    bool
     */
    protected function lockSession($session_id)
    {
        if (isset($this->lockKey)) {
            return $this->redis->setTimeout($this->lockKey, 300);
        }

        // 30 attempts to obtain a lock, in case another request already has it
        $lockKey = $this->prefixKey . $session_id . ':lock';
        $attempt = 0;

        do {
            if (($ttl = $this->redis->ttl($lockKey)) > 0) {
                sleep(1);
                continue;
            }

            if ( ! $this->redis->setex($lockKey, 300, time())) {
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
        } elseif ($ttl === -1) {
            if ($this->logger instanceof LoggerInterface) {
                $this->logger->error('SESSION_E_OBTAIN_LOCK_TTL', [$this->prefixKey . $session_id]);
            }
        }

        $this->isLocked = true;

        return true;
    }

    // ------------------------------------------------------------------------

    /**
     * RedisHandler::write
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
        if ( ! isset($this->redis)) {
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
            $this->redis->setTimeout($this->lockKey, 300);

            if ($this->fingerprint !== ($fingerprint = md5($session_data))) {
                if ($this->redis->set($this->prefixKey . $session_id, $session_data, $this->config[ 'lifetime' ])) {
                    $this->fingerprint = $fingerprint;

                    return true;
                }

                return false;
            }

            return $this->redis->setTimeout($this->prefixKey . $session_id, $this->config[ 'lifetime' ]);
        }

        return false;
    }

    //--------------------------------------------------------------------

    /**
     * RedisHandler::_lockRelease
     *
     * Releases a previously acquired lock
     *
     * @return bool
     */
    protected function lockRelease()
    {
        if (isset($this->redis, $this->lockKey) && $this->isLocked) {
            if ( ! $this->redis->delete($this->lockKey)) {
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
     * RedisHandler::isSupported
     *
     * Checks if this platform is supported on this system.
     *
     * @return bool Returns FALSE if unsupported.
     */
    public function isSupported()
    {
        return (bool)extension_loaded('redis');
    }
}