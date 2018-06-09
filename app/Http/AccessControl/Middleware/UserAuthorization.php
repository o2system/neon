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

namespace App\Http\AccessControl\Middleware;

// ------------------------------------------------------------------------

use O2System\Psr\Http\Message\ServerRequestInterface;
use O2System\Psr\Http\Server\RequestHandlerInterface;

/**
 * Class UserAuthorization
 *
 * @package Manage\Http\AccessControl\Middleware
 */
class UserAuthorization implements RequestHandlerInterface
{
    /**
     * UserAuthorization::handle
     *
     * Handles a request and produces a response
     *
     * May call other collaborating code to generate the response.
     */
    public function handle(ServerRequestInterface $request)
    {
        if( services( 'user' )->loggedIn() ) {
            if( ! services( 'user' )->authorize( $request ) ) {
                redirect_url( 'error/403' );
            }
        } else {
            redirect_url( 'login' );
        }
    }
}