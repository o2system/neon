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

use O2System\Kernel\Http\Message\Uri;
use O2System\Kernel\Http\Router\Addresses;

/**
 * Router Addresses Configuration
 *
 * @var \O2System\Kernel\Http\Router\Addresses
 */
$addresses = new Addresses();
$addresses->any(
    '/',
    function () {
        return new \App\Controllers\Login();
    }
);

$addresses->any(
    '/login',
    function () {
        return new \App\Controllers\Login();
    }
);
// ------------------------------------------------------------------------