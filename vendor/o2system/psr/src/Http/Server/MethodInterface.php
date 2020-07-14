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

/**
 * Interface MethodInterface
 * @package O2System\Psr\Http\Server
 */
interface MethodInterface
{
    /**
     * MethodInterface::HTTP_HEAD
     *
     * HTTP HEAD
     *
     * Same as GET, but transfers the status line and header section only.
     *
     * @var string
     */
    const HTTP_HEAD = 'HEAD';

    // ------------------------------------------------------------------------

    /**
     * MethodInterface::HTTP_GET
     *
     * HTTP GET
     *
     * The GET method is used to retrieve information from the given server using a given URI.
     * Requests using GET should only retrieve data and should have no other effect on the data.
     *
     * @var string
     */
    const HTTP_GET = 'GET';

    // ------------------------------------------------------------------------

    /**
     * MethodInterface::HTTP_POST
     *
     * HTTP POST
     *
     * A POST request is used to send data to the server, for example, customer information,
     * file upload, etc. using HTML forms.
     *
     * @var string
     */
    const HTTP_POST = 'POST';

    // ------------------------------------------------------------------------

    /**
     * MethodInterface::HTTP_PUT
     *
     * HTTP PUT
     *
     * Replaces all current representations of the target resource with the uploaded content.
     *
     * @var string
     */
    const HTTP_PUT = 'PUT';

    // ------------------------------------------------------------------------

    /**
     * MethodInterface::HTTP_DELETE
     *
     * HTTP DELETE
     *
     * Removes all current representations of the target resource given by a URI.
     *
     * @var string
     */
    const HTTP_DELETE = 'DELETE';

    // ------------------------------------------------------------------------

    /**
     * MethodInterface::HTTP_CONNECT
     *
     * HTTP CONNECT
     *
     * Establishes a tunnel to the server identified by a given URI.
     *
     * @var string
     */
    const HTTP_CONNECT = 'CONNECT';

    // ------------------------------------------------------------------------

    /**
     * MethodInterface::HTTP_OPTIONS
     *
     * HTTP OPTIONS
     *
     * Describes the communication options for the target resource.
     *
     * @var string
     */
    const HTTP_OPTIONS = 'OPTIONS';

    // ------------------------------------------------------------------------

    /**
     * MethodInterface::HTTP_TRACE
     *
     * HTTP TRACE
     *
     * Performs a message loop-back test along the path to the target resource.
     *
     * @var string
     */
    const HTTP_TRACE = 'TRACE';
}