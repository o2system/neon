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
 * Class UserAuthentication
 *
 * @package App\Http\AccessControl\Middleware
 */
class UserAuthentication implements MiddlewareServiceInterface
{
    /**
     * UserAuthentication::validate
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
            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * UserAuthentication::handle
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
     * UserAuthentication::terminate
     *
     * @param \O2System\Psr\Http\Message\RequestInterface $request
     *
     * @return mixed
     */
    public function terminate( RequestInterface $request )
    {
        redirect_url( 'login' );
    }
}