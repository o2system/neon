<?php
/**
 * This file is part of the O2System Content Management System package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian
 * @copyright      Copyright (c) Steeve Andrian
 */
// ------------------------------------------------------------------------

namespace App\Http\AccessControl\Middleware;

// ------------------------------------------------------------------------

use O2System\Psr\Http\Message\RequestInterface;
use O2System\Psr\Http\Middleware\MiddlewareServiceInterface;

/**
 * Class UserAuthorization
 *
 * @package App\Http\AccessControl\Middleware
 */
class UserAuthorization implements MiddlewareServiceInterface
{
    /**
     * UserAuthorization::validate
     *
     * Validate the request.
     *
     * @param \O2System\Psr\Http\Message\RequestInterface $request
     *
     * @return mixed
     */
    public function validate( RequestInterface $request )
    {
        if( services( 'user' )->loggedIn() ) {
            if( services( 'user' )->authorize( $request ) ) {
                return true;
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * UserAuthorization::handle
     *
     * Handle the valid request.
     *
     * @param \O2System\Psr\Http\Message\RequestInterface $request
     *
     * @return mixed
     */
    public function handle( RequestInterface $request )
    {

    }

    // ------------------------------------------------------------------------

    /**
     * UserAuthorization::terminate
     *
     * @param \O2System\Psr\Http\Message\RequestInterface $request
     *
     * @return mixed
     */
    public function terminate( RequestInterface $request )
    {
        if( services( 'user' )->loggedIn() ) {
            redirect_url( 'error/403' );
        } else {
            redirect_url( 'login' );
        }
    }
}