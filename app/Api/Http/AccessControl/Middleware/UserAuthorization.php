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

namespace App\Api\Http\AccessControl\Middleware;

// ------------------------------------------------------------------------

use O2System\Psr\Http\Message\ServerRequestInterface;

/**
 * Class UserAuthorization
 *
 * @package App\Api\Http\AccessControl\Middleware
 */
class UserAuthorization extends \App\Http\AccessControl\Middleware\UserAuthorization
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
        parent::handle($request);

        if (services('user')->loggedIn()) {
            if ( ! services('user')->hasAccess($request->getUri()->getSegments()->getParts())) {
                output()->sendError(403);
            }
        } else {
            output()->sendError(403);
        }
    }
}