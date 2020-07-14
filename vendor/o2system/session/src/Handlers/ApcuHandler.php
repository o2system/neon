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
 * Class ApcuHandler
 *
 * @package O2System\Session\Handlers
 */
class ApcuHandler extends AbstractHandler
{
    /**
     * Platform Name
     *
     * @access  protected
     * @var string
     */
    protected $platform = 'apcu';

    // ------------------------------------------------------------------------

    /**
     * ApcHandler::open
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
                $this->logger->error('SESSION_E_PLATFORM_UNSUPPORTED', ['APC User Cache (APCu)']);
            }

            return $this->failure;
        }

        return $this->success;
    }

    // ------------------------------------------------------------------------

    /**
     * ApcHandler::isSupported
     *
     * Checks if this platform is supported on this system.
     *
     * @return bool Returns FALSE if unsupported.
     */
    public function isSupported()
    {
        return (bool)(extension_loaded('apcu') && ini_get('apc.enabled'));
    }

    // ------------------------------------------------------------------------

    /**
     * ApcHandler::close
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
            return apcu_delete($this->lockKey);
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ApcHandler::destroy
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
            apcu_delete($this->prefixKey . $session_id);

            return $this->destroyCookie();
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ApcHandler::gc
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
        // Not necessary, APC takes care of that.
        return true;
    }

    // ------------------------------------------------------------------------

    /**
     * ApcHandler::read
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
            $sessionData = apcu_fetch($this->prefixKey . $session_id, $success);

            if ($success) {
                $this->fingerprint = md5($sessionData);

                return $sessionData;
            }
        }

        return '';
    }

    // ------------------------------------------------------------------------

    /**
     * ApcHandler::_lockSession
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
            return apcu_store($this->lockKey, time(), 300);
        }

        // 30 attempts to obtain a lock, in case another request already has it
        $lockKey = $this->prefixKey . $session_id . ':lock';
        $attempt = 0;

        do {
            if (apcu_exists($lockKey)) {
                sleep(1);
                continue;
            }

            if ( ! apcu_store($lockKey, time(), 300)) {
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
     * ApcHandler::write
     *
     * Write session data
     *
     * @link  http://php.net/manual/en/sessionhandlerinterface.write.php
     *
     * @param string $session_id   The session id.
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
    public function write($session_id, $sessionData)
    {
        if ($session_id !== $this->sessionId) {
            if ( ! $this->lockRelease() OR ! $this->lockSession($session_id)) {
                return false;
            }

            $this->fingerprint = md5('');
            $this->sessionId = $session_id;
        }

        if (isset($this->lockKey)) {
            apcu_store($this->lockKey, time(), 300);

            if ($this->fingerprint !== ($fingerprint = md5($sessionData))) {
                if (apcu_store($this->prefixKey . $session_id, $sessionData, $this->config[ 'lifetime' ])) {
                    $this->fingerprint = $fingerprint;

                    return true;
                }

                return false;
            }

            return apcu_store($this->prefixKey . $session_id, $sessionData, $this->config[ 'lifetime' ]);
        }

        return false;
    }

    //--------------------------------------------------------------------

    /**
     * ApcHandler::_lockRelease
     *
     * Releases a previously acquired lock
     *
     * @return    bool
     */
    protected function lockRelease()
    {
        if (isset($this->lockKey) AND $this->isLocked) {
            if ( ! apcu_delete($this->lockKey)) {
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