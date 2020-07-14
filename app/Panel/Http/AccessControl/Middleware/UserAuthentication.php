<?php
/**
 * This file is part of the WebIn Platform.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @author         PT. Lingkar Kreasi (Circle Creative)
 *  @copyright      Copyright (c) PT. Lingkar Kreasi (Circle Creative)
 */

// ------------------------------------------------------------------------

namespace App\Panel\Http\AccessControl\Middleware;

// ------------------------------------------------------------------------

use O2System\Psr\Http\Message\ServerRequestInterface;
use O2System\Psr\Http\Server\RequestHandlerInterface;

/**
 * Class UserAuthentication
 *
 * @package App\Http\AccessControl\Middleware
 */
class UserAuthentication implements RequestHandlerInterface
{
    /**
     * UserAuthentication::handle
     *
     * Handles a request and produces a response
     *
     * May call other collaborating code to generate the response.
     * @param ServerRequestInterface $request
     */
    public function handle(ServerRequestInterface $request)
    {
        if (! services('accessControl')->loggedIn()) {
            redirect_url('login');
        }
    }
}
