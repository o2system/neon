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

namespace O2System\Psr\Http\Server;

// ------------------------------------------------------------------------

use O2System\Psr\Http\Message\ServerRequestInterface;

/**
 * Interface RequestHandlerInterface
 *
 * Handles a server request and produces a response
 *
 * An HTTP request handler process an HTTP request in order to produce an
 * HTTP response.
 *
 * @package O2System\Psr\Http
 */
interface RequestHandlerInterface
{
    /**
     * Handles a request and produces a response
     *
     * May call other collaborating code to generate the response.
     */
    public function handle(ServerRequestInterface $request);
}