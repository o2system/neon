<?php
/**
 * This file is part of the O2System PHP Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */

// ------------------------------------------------------------------------

/**
 * Http Session Configuration
 *
 * @see https://github.com/o2system/session/wiki
 *
 * @var \O2System\Session\DataStructures\Config
 */
$session = new \O2System\Session\DataStructures\Config([
    /**
     * Session Handler
     *
     * Supported session handler:
     * - Apc
     * - Apcu
     * - File
     * - Memcached
     * - Memcache
     * - Redis
     *
     * @var string
     */
    'handler' => 'file',

    // ------------------------------------------------------------------------

    /**
     * Session Cookie
     *
     * @var array
     */
    'cookie' => [

        /**
         * Session Cookie Lifetime
         *
         * The number of SECONDS the cookie to last.
         *
         * @var int
         */
        'lifetime' => 7200,

        /**
         * Session Cookie Domain
         *
         * When set to blank or null, will automatically set base on your server hostname.
         *
         * @var string
         */
        'domain' => null,

        /**
         * Session Cookie Path
         *
         * Typically will be a forward slash.
         *
         * @var string
         */
        'path' => '/',

        /**
         * Session Cookie Secure
         *
         * Cookie will only be set if a secure HTTPS connection exists.
         *
         * @var bool
         */
        'secure' => false,

        /**
         * Session Cookie Http Only
         *
         * Cookie will only be accessible via HTTP(S) (no javascript).
         *
         * @var bool
         */
        'httpOnly' => false,
    ],
]);
