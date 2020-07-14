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

use Psr\Log\LoggerInterface;
use O2System\Session\Abstracts\AbstractHandler;

/**
 * Class WincacheHandler
 *
 * @package O2System\Session\Handlers
 */
class WincacheHandler extends AbstractHandler
{
    /**
     * Platform Name
     *
     * @access  protected
     * @var string
     */
    protected $platform = 'wincache';

    // ------------------------------------------------------------------------

    /**
     * WincacheHandler::open
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
        if ($this->isSupported() === false) {
            if ($this->logger instanceof LoggerInterface) {
                $this->logger->error('SESSION_E_PLATFORM_UNSUPPORTED', [$this->platform]);
            }

            return false;
        }

        return true;
    }

    // ------------------------------------------------------------------------

    /**
     * WincacheHandler::isSupported
     *
     * Checks if this platform is supported on this system.
     *
     * @return bool Returns FALSE if unsupported.
     */
    public function isSupported()
    {
        return (bool)(extension_loaded('wincache') && ini_get('wincache.ucenabled'));
    }

    // ------------------------------------------------------------------------

    /**
     * WincacheHandler::close
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
        if (isset($this->lockKey)) {
            return wincache_ucache_delete($this->lockKey);
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * WincacheHandler::destroy
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
        if (isset($this->lockKey)) {
            wincache_ucache_delete($this->prefixKey . $session_id);

            return $this->destroyCookie();
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * WincacheHandler::gc
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
        // Not necessary, Wincache takes care of that.
        return true;
    }

    // ------------------------------------------------------------------------

    /**
     * WincacheHandler::read
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
        if ($this->lockSession($session_id)) {
            // Needed by write() to detect session_regenerate_id() calls
            $this->sessionId = $session_id;

            $success = false;
            $sessionData = wincache_ucache_get($this->prefixKey . $session_id, $success);

            if ($success) {
                $this->fingerprint = md5($sessionData);

                return $sessionData;
            }
        }

        return '';
    }

    // ------------------------------------------------------------------------

    /**
     * WincacheHandler::_lockSession
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
            return wincache_ucache_set($this->lockKey, time(), 300);
        }

        // 30 attempts to obtain a lock, in case another request already has it
        $lockKey = $this->prefixKey . $session_id . ':lock';
        $attempt = 0;

        do {
            if (wincache_ucache_exists($lockKey)) {
                sleep(1);
                continue;
            }

            if ( ! wincache_ucache_set($lockKey, time(), 300)) {
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

    //--------------------------------------------------------------------

    /**
     * WincacheHandler::write
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
        if ($session_id !== $this->sessionId) {
            if ( ! $this->lockRelease() OR ! $this->lockSession($session_id)) {
                return false;
            }

            $this->fingerprint = md5('');
            $this->sessionId = $session_id;
        }

        if (isset($this->lockKey)) {
            wincache_ucache_set($this->lockKey, time(), 300);

            if ($this->fingerprint !== ($fingerprint = md5($session_data))) {
                if (wincache_ucache_set(
                    $this->prefixKey . $session_id,
                    $session_data,
                    $this->config[ 'lifetime' ]
                )) {
                    $this->fingerprint = $fingerprint;

                    return true;
                }

                return false;
            }

            return wincache_ucache_set($this->prefixKey . $session_id, $session_data, $this->config[ 'lifetime' ]);
        }

        return false;
    }

    //--------------------------------------------------------------------

    /**
     * WincacheHandler::_lockRelease
     *
     * Releases a previously acquired lock
     *
     * @return    bool
     */
    protected function lockRelease()
    {
        if (isset($this->lockKey) AND $this->isLocked) {
            if ( ! wincache_ucache_delete($this->lockKey)) {
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
}