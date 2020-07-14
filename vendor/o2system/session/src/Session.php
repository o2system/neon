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

namespace O2System;

// ------------------------------------------------------------------------

use O2System\Kernel\Http\Message\Uri\Domain;
use O2System\Session\Abstracts\AbstractHandler;
use Psr\Log\LoggerInterface;

/**
 * Class Session
 *
 * @package O2System
 */
class Session extends \O2System\Kernel\DataStructures\Input\Session
{
    /**
     * Session::$config
     *
     * Session Config
     *
     * @var Kernel\DataStructures\Config
     */
    protected $config;

    /**
     * Session::$handler
     *
     * Logger Instance
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Session::$handler
     *
     * Session Cache Platform Handler
     *
     * @var AbstractHandler
     */
    protected $handler;

    /**
     * Session::$sidRegexp
     *
     * @var
     */
    protected $sidRegexp;

    // ------------------------------------------------------------------------

    /**
     * Session::__construct
     *
     * @param Kernel\DataStructures\Config $config
     *
     * @return Session
     */
    public function __construct(Kernel\DataStructures\Config $config)
    {
        language()
            ->addFilePath(__DIR__ . DIRECTORY_SEPARATOR)
            ->loadFile('session');

        $this->config = $config;

        if ($this->config->cookie->wildcard === false) {
            $this->config->cookie->domain = (new Domain())->__toString();
        } else {
            $this->config->cookie->domain = '.' . ltrim($this->config->cookie->domain, '.');
        }

        if ($this->config->offsetExists('handler')) {
            $handlerClassName = '\O2System\Session\Handlers\\' . ucfirst($this->config->handler) . 'Handler';

            if (class_exists($handlerClassName)) {
                $this->handler = new $handlerClassName(clone $this->config);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Session::isSupported
     *
     * Checks if server is support cache storage platform.
     *
     * @param string $platform Platform name.
     *
     * @return bool
     */
    public static function isSupported($platform)
    {
        $handlerClassName = '\O2System\Session\Handlers\\' . ucfirst($platform) . 'Handler';

        if (class_exists($handlerClassName)) {
            return (new $handlerClassName)->isSupported();
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Session::setLogger
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

        // Load Session Language
        language()->loadFile('session');

        if (isset($this->handler)) {
            $this->handler->setLogger($this->logger);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Session::start
     *
     * Initialize Native PHP Session.
     *
     * @return void
     */
    public function start()
    {
        if (php_sapi_name() === 'cli') {
            if ($this->logger instanceof LoggerInterface) {
                $this->logger->debug('DEBUG_SESSION_CLI_ABORTED');
            }

            return;
        } elseif ((bool)ini_get('session.auto_start')) {
            if ($this->logger instanceof LoggerInterface) {
                $this->logger->error('DEBUG_SESSION_AUTO_START_ABORTED');
            }

            return;
        } elseif (session_status() === PHP_SESSION_ACTIVE)
        {
            $this->logger->warning('Session: Sessions is enabled, and one exists.Please don\'t $session->start();');
            return;
        }

        if ( ! $this->handler instanceof \SessionHandlerInterface) {
            $this->logger->error('E_SESSION_HANDLER_INTERFACE', [$this->handler->getPlatform()]);
        }

        $this->setConfiguration();

        session_set_save_handler($this->handler, true);

        // Sanitize the cookie, because apparently PHP doesn't do that for userspace handlers
        if (isset($_COOKIE[ $this->config[ 'name' ] ]) && (
                ! is_string($_COOKIE[ $this->config[ 'name' ] ]) || ! preg_match('#\A' . $this->sidRegexp . '\z#',
                    $_COOKIE[ $this->config[ 'name' ] ])
            )
        ) {
            unset($_COOKIE[ $this->config[ 'name' ] ]);
        }

        if(session_id() === '' || !isset($_SESSION)) {
            // session isn't started
            session_start();
        }
        
        $this->storage =& $_SESSION;

        // Sanitize the cookie, because apparently PHP doesn't do that for userspace handlers
        if (isset($_COOKIE[ $this->config[ 'name' ] ]) && (
                ! is_string($this->config[ 'name' ]) ||
                ! preg_match('#\A' . $this->sidRegexp . '\z#', $_COOKIE[ $this->config[ 'name' ] ]
                )
            )
        ) {
            unset($_COOKIE[ $this->config[ 'name' ] ]);
        }

        // Is session ID auto-regeneration configured? (ignoring ajax requests)
        if ((empty($_SERVER[ 'HTTP_X_REQUESTED_WITH' ]) ||
                strtolower($_SERVER[ 'HTTP_X_REQUESTED_WITH' ]) !== 'xmlhttprequest') &&
            ($regenerateTime = $this->config[ 'regenerate' ]->lifetime) > 0
        ) {
            if ( ! isset($_SESSION[ 'last_regenerate' ])) {
                $_SESSION[ 'last_regenerate' ] = time();
            } elseif ($_SESSION[ 'last_regenerate' ] < (time() - $regenerateTime)) {
                $this->regenerate();
            }
        }

        // Another work-around ... PHP doesn't seem to send the session cookie
        // unless it is being currently created or regenerated
        elseif (isset($_COOKIE[ $this->config[ 'name' ] ]) && $_COOKIE[ $this->config[ 'name' ] ] === session_id()
        ) {
            setcookie(
                $this->config[ 'name' ],
                session_id(),
                (empty($this->config[ 'lifetime' ]) ? 0 : time() + $this->config[ 'lifetime' ]),
                $this->config[ 'cookie' ]->path,
                $this->config[ 'cookie' ]->domain,
                $this->config[ 'cookie' ]->secure,
                true
            );
        }

        $this->initializeVariables();

        if ($this->logger instanceof LoggerInterface) {
            $this->logger->debug('DEBUG_SESSION_INITIALIZED', [$this->handler->getPlatform()]);
        }
    }

    //--------------------------------------------------------------------

    /**
     * Session::setConfiguration
     *
     * Handle input binds and configuration defaults.
     *
     * @return void
     */
    private function setConfiguration()
    {
        ini_set('session.name', $this->config[ 'name' ]);

        if (empty($this->config[ 'lifetime' ])) {
            $this->config[ 'lifetime' ] = (int)ini_get('session.gc_maxlifetime');
        } else {
            ini_set('session.gc_maxlifetime', (int)$this->config[ 'lifetime' ]);
        }

        ini_set('session.cookie_domain', $this->config[ 'cookie' ]->domain);
        ini_set('session.cookie_path', $this->config[ 'cookie' ]->path);

        // Security is king
        ini_set('session.cookie_lifetime', $this->config[ 'lifetime' ]);
        ini_set('session.use_cookies', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_httponly', 1);
        ini_set('ssession.cookie_secure', (is_https() ? 1 : 0));
        ini_set('session.use_trans_sid', 0);
        
        session_set_cookie_params(
            (empty($this->config[ 'lifetime' ]) ? 0 : time() + $this->config[ 'lifetime' ]),
            $this->config[ 'cookie' ]->path,
            $this->config[ 'cookie' ]->domain,
            $this->config[ 'cookie' ]->secure,
            true
        );

        $this->configureSidLength();
    }

    //--------------------------------------------------------------------

    /**
     * Configure session ID length
     *
     * To make life easier, we used to force SHA-1 and 4 bits per
     * character on everyone. And of course, someone was unhappy.
     *
     * Then PHP 7.1 broke backwards-compatibility because ext/session
     * is such a mess that nobody wants to touch it with a pole stick,
     * and the one guy who does, nobody has the energy to argue with.
     *
     * So we were forced to make changes, and OF COURSE something was
     * going to break and now we have this pile of shit. -- Narf
     *
     * @return    void
     */
    protected function configureSidLength()
    {
        $bitsPerChars = (int)(ini_get('session.sid_bits_per_character') !== false
            ? ini_get('session.sid_bits_per_character')
            : 4);
        $sidLength = (int)(ini_get('session.sid_length') !== false
            ? ini_get('session.sid_length')
            : 40);
        if (($sidLength * $bitsPerChars) < 160) {
            $bits = ($sidLength * $bitsPerChars);
            // Add as many more characters as necessary to reach at least 160 bits
            $sidLength += (int)ceil((160 % $bits) / $bitsPerChars);
            ini_set('session.sid_length', $sidLength);
        }
        // Yes, 4,5,6 are the only known possible values as of 2016-10-27
        switch ($bitsPerChars) {
            case 4:
                $this->sidRegexp = '[0-9a-f]';
                break;
            case 5:
                $this->sidRegexp = '[0-9a-v]';
                break;
            case 6:
                $this->sidRegexp = '[0-9a-zA-Z,-]';
                break;
        }

        $this->sidRegexp .= '{' . $sidLength . '}';
    }

    //--------------------------------------------------------------------

    /**
     * Session::initializeVariables
     *
     * Handle flash and temporary session variables. Clears old "flash" session variables,
     * marks the new one for deletion and handles "temp" session variables deletion.
     *
     * @return void
     */
    private function initializeVariables()
    {
        if (empty($_SESSION[ 'system_variables' ])) {
            return;
        }

        $currentTime = time();

        foreach ($_SESSION[ 'system_variables' ] as $key => &$value) {
            if ($value === 'new') {
                $_SESSION[ 'system_variables' ][ $key ] = 'old';
            }
            // Hacky, but 'old' will (implicitly) always be less than time() ;)
            // DO NOT move this above the 'new' check!
            elseif ($value < $currentTime) {
                unset($_SESSION[ $key ], $_SESSION[ 'system_variables' ][ $key ]);
            }
        }

        if (empty($_SESSION[ 'system_variables' ])) {
            unset($_SESSION[ 'system_variables' ]);
        }
    }

    //--------------------------------------------------------------------

    /**
     * Session::regenerate
     *
     * Regenerates the session ID
     *
     * @return void
     */
    public function regenerate($destroy = false)
    {
        $destroy = empty($destroy) ? $this->config[ 'regenerate' ]->destroy : $destroy;

        $_SESSION[ 'last_regenerate' ] = time();
        session_regenerate_id($destroy);
    }

    //--------------------------------------------------------------------

    /**
     * Session::isStarted
     *
     * Check if the PHP Session is has been started.
     *
     * @access  public
     * @return  bool
     */
    public function isStarted()
    {
        if (php_sapi_name() !== 'cli') {
            return session_status() === PHP_SESSION_ACTIVE ? true : false;
        }

        return false;
    }

    //--------------------------------------------------------------------

    /**
     * Does a full stop of the session:
     *
     * - destroys the session
     * - unsets the session id
     * - destroys the session cookie
     */
    public function stop()
    {
        setcookie(
            $this->config[ 'name' ],
            session_id(),
            1,
            $this->config[ 'cookie' ]->path,
            $this->config[ 'cookie' ]->domain,
            $this->config[ 'cookie' ]->secure,
            true
        );

        session_regenerate_id(true);
    }

    // ------------------------------------------------------------------------

    /**
     * Session::destroy
     *
     * Destroying current session
     *
     * @param callable $destructionCallback Callback destruction.
     *
     * @return array Array of old storage items.
     */
    public function destroy($destructionCallback = null)
    {
        session_destroy();
    }

    // ------------------------------------------------------------------------

    /**
     * Session::__get
     *
     * Implementing magic method __get to simplify gets PHP native session variable by requested offset,
     * just simply calling isset( $session[ 'offset' ] ).
     *
     * @param $offset
     *
     * @return mixed
     */
    public function &__get($offset)
    {
        if ($offset === 'id') {
            $this->storage[ 'id' ] = session_id();
        }

        if ( ! isset($this->storage[ $offset ])) {
            $this->storage[ $offset ] = null;
        }

        return $this->storage[ $offset ];
    }

    // ------------------------------------------------------------------------

    /**
     * Session::setFlash
     *
     * Sets flash data into the session that will only last for a single request.
     * Perfect for use with single-use status update messages.
     *
     * If $offset is an array, it is interpreted as an associative array of
     * key/value pairs for flash session variables.
     * Otherwise, it is interpreted as the identifier of a specific
     * flash session variable, with $value containing the property value.
     *
     * @param mixed      $offset Flash session variable string offset identifier or associative array of values.
     * @param mixed|null $value  Flash session variable offset value.
     */
    public function setFlash($offset, $value = null)
    {
        $this->set($offset, $value);
        $this->markFlash(is_array($offset) ? array_keys($offset) : $offset);
    }

    //--------------------------------------------------------------------

    /**
     * Session::set
     *
     * Sets session data into PHP native session global variable.
     *
     * If $offset is a string, then it is interpreted as a session property
     * key, and  $value is expected to be non-null.
     *
     * If $offset is an array, it is expected to be an array of key/value pairs
     * to be set as session values.
     *
     * @param string $offset Session offset or associative array of session values
     * @param mixed  $value  Session offset value.
     */
    public function set($offset, $value = null)
    {
        if (is_array($offset)) {
            foreach ($offset as $key => &$value) {
                $_SESSION[ $key ] = $value;
            }

            return;
        }

        $_SESSION[ $offset ] =& $value;
    }

    //--------------------------------------------------------------------

    /**
     * Session::markAsFlash
     *
     * Mark an session offset variables as flash session variables.
     *
     * @param string|array $offset Flash session variable string offset identifier or array of offsets.
     *
     * @return bool Returns FALSE if any flash session variables are not already set.
     */
    public function markFlash($offset)
    {
        if (is_array($offset)) {
            for ($i = 0, $c = count($offset); $i < $c; $i++) {
                if ( ! isset($_SESSION[ $offset[ $i ] ])) {
                    return false;
                }
            }

            $new = array_fill_keys($offset, 'new');

            $_SESSION[ 'system_variables' ] = isset($_SESSION[ 'system_variables' ]) ? array_merge(
                $_SESSION[ 'system_variables' ],
                $new
            ) : $new;

            return true;
        }

        if ( ! isset($_SESSION[ $offset ])) {
            return false;
        }

        $_SESSION[ 'system_variables' ][ $offset ] = 'new';

        return true;
    }

    // ------------------------------------------------------------------------

    /**
     * Session::offsetGet
     *
     * Gets PHP native session variable value by requested offset.
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     *                      </p>
     *
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        if ($offset === 'id') {
            $_SESSION[ 'id' ] = session_id();
        }

        return (isset($_SESSION[ $offset ])) ? $_SESSION[ $offset ] : false;
    }

    //--------------------------------------------------------------------

    /**
     * Session::getFlash
     *
     * Retrieve one or more items of flash data from the session.
     * If the offset is null, it will returns all flash session variables.
     *
     * @param string $offset Flash session variable string offset identifier
     *
     * @return array|null    The requested property value, or an assoo2systemative array  of them
     */
    public function getFlash($offset = null)
    {
        if (isset($offset)) {
            return (isset($_SESSION[ 'system_variables' ], $_SESSION[ 'system_variables' ][ $offset ], $_SESSION[ $offset ]) &&
                ! is_int($_SESSION[ 'system_variables' ][ $offset ])) ? $_SESSION[ $offset ] : null;
        }

        $flashVariables = [];

        if ( ! empty($_SESSION[ 'system_variables' ])) {
            foreach ($_SESSION[ 'system_variables' ] as $offset => &$value) {
                is_int($value) OR $flashVariables[ $offset ] = $_SESSION[ $offset ];
            }
        }

        return $flashVariables;
    }

    //--------------------------------------------------------------------

    /**
     * Session::keepFlash
     *
     * Keeps a single piece of flash data alive for one more request.
     *
     * @param string|array $offset Flash session variable string offset identifier or array of offsets.
     */
    public function keepFlash($offset)
    {
        $this->markFlash($offset);
    }

    //--------------------------------------------------------------------

    /**
     * Session::unsetFlash
     *
     * Unset flash session variables.
     *
     * @param string $offset Flash session variable string offset identifier or array of offsets.
     */
    public function unsetFlash($offset)
    {
        if (empty($_SESSION[ 'system_variables' ])) {
            return;
        }

        is_array($offset) OR $offset = [$offset];

        foreach ($offset as $key) {
            if (isset($_SESSION[ 'system_variables' ][ $key ]) && ! is_int(
                    $_SESSION[ 'system_variables' ][ $key ]
                )
            ) {
                unset($_SESSION[ 'system_variables' ][ $key ]);
            }
        }

        if (empty($_SESSION[ 'system_variables' ])) {
            unset($_SESSION[ 'system_variables' ]);
        }
    }

    //--------------------------------------------------------------------

    /**
     * Session::getFlashOffsets
     *
     * Gets all flash session variable offsets.
     *
     * @return array Returns array of flash session variable offsets.
     */
    public function getFlashOffsets()
    {
        if ( ! isset($_SESSION[ 'system_variables' ])) {
            return [];
        }

        $offsets = [];
        foreach (array_keys($_SESSION[ 'system_variables' ]) as $offset) {
            is_int($_SESSION[ 'system_variables' ][ $offset ]) OR $offsets[] = $offset;
        }

        return $offsets;
    }

    //--------------------------------------------------------------------

    /**
     * Session::setTemp
     *
     * Sets temporary session variable.
     *
     * @param mixed $offset Temporary session variable string offset identifier or array of offsets.
     * @param null  $value  Temporary session variable offset value.
     * @param int   $ttl    Temporary session variable Time-to-live in seconds
     */
    public function setTemp($offset, $value = null, $ttl = 300)
    {
        $this->set($offset, $value);
        $this->markTemp(is_array($offset) ? array_keys($offset) : $offset, $ttl);
    }

    //--------------------------------------------------------------------

    /**
     * Session::markTemp
     *
     * Mark one of more pieces of data as being temporary, meaning that
     * it has a set lifespan within the session.
     *
     * @param string $offset Temporary session variable string offset identifier.
     * @param int    $ttl    Temporary session variable Time-to-live, in seconds
     *
     * @return bool Returns FALSE if none temporary session variable is set.
     */
    public function markTemp($offset, $ttl = 300)
    {
        $ttl += time();

        if (is_array($offset)) {
            $temp = [];

            foreach ($offset as $key => $value) {
                // Do we have a key => ttl pair, or just a key?
                if (is_int($key)) {
                    $key = $value;
                    $value = $ttl;
                } else {
                    $value += time();
                }

                if ( ! isset($_SESSION[ $key ])) {
                    return false;
                }

                $temp[ $key ] = $value;
            }

            $_SESSION[ 'system_variables' ] = isset($_SESSION[ 'system_variables' ]) ? array_merge(
                $_SESSION[ 'system_variables' ],
                $temp
            ) : $temp;

            return true;
        }

        if ( ! isset($_SESSION[ $offset ])) {
            return false;
        }

        $_SESSION[ 'system_variables' ][ $offset ] = $ttl;

        return true;
    }

    //--------------------------------------------------------------------

    /**
     * Session::getTemp
     *
     * Gets either a single piece or all temporary session variables.
     *
     * @param string $offset Temporary session variable string offset identifier.
     *
     * @return array Returns temporary session variables.
     */
    public function getTemp($offset = null)
    {
        if (isset($offset)) {
            return (isset($_SESSION[ 'system_variables' ], $_SESSION[ 'system_variables' ][ $offset ], $_SESSION[ $offset ]) &&
                is_int($_SESSION[ 'system_variables' ][ $offset ])) ? $_SESSION[ $offset ] : null;
        }

        $tempVariables = [];

        if ( ! empty($_SESSION[ 'system_variables' ])) {
            foreach ($_SESSION[ 'system_variables' ] as $offset => &$value) {
                is_int($value) && $tempVariables[ $offset ] = $_SESSION[ $offset ];
            }
        }

        return $tempVariables;
    }

    // ------------------------------------------------------------------------

    /**
     * Session::unsetTemp
     *
     * Unset temporary session variable.
     *
     * @param mixed $offset Temporary session variable string offset identifier.
     */
    public function unsetTemp($offset)
    {
        if (empty($_SESSION[ 'system_variables' ])) {
            return;
        }

        is_array($offset) OR $offset = [$offset];

        foreach ($offset as $key) {
            if (isset($_SESSION[ 'system_variables' ][ $key ]) && is_int(
                    $_SESSION[ 'system_variables' ][ $key ]
                )
            ) {
                unset($_SESSION[ 'system_variables' ][ $key ]);
            }
        }

        if (empty($_SESSION[ 'system_variables' ])) {
            unset($_SESSION[ 'system_variables' ]);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Session::getTempOffsets
     *
     * Gets all temporary session variable offsets identifier.
     *
     * @return array Returns array of temporary session variable offsets identifier.
     */
    public function getTempOffsets()
    {
        if ( ! isset($_SESSION[ 'system_variables' ])) {
            return [];
        }

        $offsets = [];
        foreach (array_keys($_SESSION[ 'system_variables' ]) as $offset) {
            is_int($_SESSION[ 'system_variables' ][ $offset ]) && $offsets[] = $offset;
        }

        return $offsets;
    }
}