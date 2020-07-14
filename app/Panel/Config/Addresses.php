<?php
/**
 * This file is part of the Circle Creative Web Application Project Boilerplate.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @author         PT. Lingkar Kreasi (Circle Creative)
 *  @copyright      Copyright (c) PT. Lingkar Kreasi (Circle Creative)
 */
// ------------------------------------------------------------------------

use O2System\Kernel\Http\Router\Addresses;

$addresses = new Addresses();

// ------------------------------------------------------------------------

// CMS Default Controller
$addresses->any(
    '/',
    function () {
        return new \App\Panel\Controllers\Login();
    }
);

$addresses->any(
    '/login',
    function () {
        return new \App\Panel\Controllers\Login();
    }
);

// ------------------------------------------------------------------------

$addresses->any(
    '/logout',
    function () {
        services('accessControl')->logout();
        redirect_url('login');
    }
);
