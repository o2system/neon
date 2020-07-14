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

namespace O2System\Session\DataStructures;

// ------------------------------------------------------------------------

use O2System\Kernel\DataStructures;

/**
 * Class Config
 *
 * @package O2System\Session\Metadata
 */
class Config extends DataStructures\Config
{
    /**
     * Config::__construct
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        // Define Session Name
        $config[ 'name' ] = isset($config[ 'name' ]) ? $config[ 'name' ] : 'o2session';

        // Define Session Match IP
        $config[ 'match' ][ 'ip' ] = isset($config[ 'match' ][ 'ip' ]) ? $config[ 'match' ][ 'ip' ] : false;

        // Re-Define Session Name base on Match IP
        $config[ 'name' ] = $config[ 'name' ] . ':' . ($config[ 'match' ][ 'ip' ] ? $_SERVER[ 'REMOTE_ADDR' ] . ':' : '');
        $config[ 'name' ] = rtrim($config[ 'name' ], ':');

        if (isset($config[ 'handler' ])) {
            $config[ 'handler' ] = $config[ 'handler' ] === 'files' ? 'file' : $config[ 'handler' ];
        }

        if ($config[ 'handler' ] === 'file') {
            if (isset($config[ 'filePath' ])) {
                $config[ 'filePath' ] = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $config[ 'filePath' ]);

                if ( ! is_dir($config[ 'filePath' ])) {
                    if (defined('PATH_CACHE')) {
                        $config[ 'filePath' ] = PATH_CACHE . $config[ 'filePath' ];
                    } else {
                        $config[ 'filePath' ] = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $config[ 'filePath' ];
                    }
                }
            } elseif (defined('PATH_CACHE')) {
                $config[ 'filePath' ] = PATH_CACHE . 'sessions';
            } else {
                $this->path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . implode(
                        DIRECTORY_SEPARATOR,
                        ['o2system', 'cache', 'sessions']
                    );
            }

            $config[ 'filePath' ] = rtrim($config[ 'filePath' ], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

            if ( ! is_writable($config[ 'filePath' ])) {
                if ( ! file_exists($config[ 'filePath' ])) {
                    @mkdir($config[ 'filePath' ], 0777, true);
                }
            }
        }

        if (empty($config[ 'cookie' ]) AND php_sapi_name() !== 'cli') {
            $config[ 'cookie' ] = [
                'name'     => 'o2session',
                'lifetime' => 7200,
                'domain'   => isset($_SERVER[ 'HTTP_HOST' ]) ? $_SERVER[ 'HTTP_HOST' ] : $_SERVER[ 'SERVER_NAME' ],
                'path'     => '/',
                'secure'   => false,
                'httpOnly' => false,
            ];
        }

        if (empty($config[ 'cookie' ][ 'wildcard' ])) {
            $config[ 'cookie' ][ 'wildcard' ] = true;
        }

        if ( ! isset($config[ 'regenerate' ])) {
            $config[ 'regenerate' ][ 'destroy' ] = false;
            $config[ 'regenerate' ][ 'lifetime' ] = 600;
        }

        if ( ! isset($config[ 'lifetime' ])) {
            $config[ 'lifetime' ] = $config[ 'cookie' ][ 'lifetime' ];
        }

        if ( ! isset($config[ 'path' ])) {
            $config[ 'path' ] = '/';
        }

        parent::__construct($config, Config::CAMELCASE_OFFSET);
    }
}