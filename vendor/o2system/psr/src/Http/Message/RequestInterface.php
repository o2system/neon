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

namespace O2System\Psr\Http\Message;

// ------------------------------------------------------------------------

/**
 * Interface RequestInterface
 *
 * Representation of an outgoing, client-side request.
 *
 * Per the HTTP specification, this interface includes properties for
 * each of the following:
 *
 * - Protocol version
 * - HTTP method
 * - URI
 * - Headers
 * - Message body
 *
 * During construction, implementations MUST attempt to set the Host header from
 * a provided URI if no Host header is provided.
 *
 * Requests are considered immutable; all methods that might change state MUST
 * be implemented such that they retain the internal state of the current
 * message and return an instance that contains the changed state.
 *
 * @package O2System\Psr\Http\Message
 */
interface RequestInterface extends MessageInterface
{
    /**
     * RequestInterface::METHOD_OPTIONS
     *
     * The OPTIONS method represents a request for information about the communication options
     * available on the request/response chain identified by the Request-URI.
     * This method allows the client to determine the options and/or requirements associated with a resource,
     * or the capabilities of a server, without implying a resource action or initiating a resource retrieval.
     *
     * @var string
     */
    const METHOD_OPTIONS = 'OPTIONS';

    /**
     * RequestInterface::METHOD_GET
     *
     * The GET method means retrieve whatever information (in the form of an entity) is identified
     * by the Request-URI. If the Request-URI refers to a data-producing process, it is the produced
     * data which shall be returned as the entity in the response and not the source text of the process,
     * unless that text happens to be the output of the process.
     *
     * @var string
     */
    const METHOD_GET = 'GET';

    /**
     * RequestInterface::METHOD_HEAD
     *
     * The HEAD method is identical to GET except that the server MUST NOT return a message-body in the response.
     * The metainformation contained in the HTTP headers in response to a HEAD request SHOULD be identical to
     * the information sent in response to a GET request.
     *
     * This method can be used for obtaining metainformation about the entity implied by the request
     * without transferring the entity-body itself. This method is often used for testing hypertext links
     * for validity, accessibility, and recent modification.
     *
     * The response to a HEAD request MAY be cacheable in the sense that the information contained in
     * the response MAY be used to update a previously cached entity from that resource.
     * If the new field values indicate that the cached entity differs from the current entity
     * (as would be indicated by a change in Content-Length, Content-MD5, ETag or Last-Modified),
     * then the cache MUST treat the cache entry as stale.
     *
     * @var string
     */
    const METHOD_HEAD = 'HEAD';

    /**
     * RequestInterface::METHOD_PATCH
     *
     *
     *
     * @var string
     */
    const METHOD_PATCH = 'PATCH';

    /**
     * RequestInterface::METHOD_POST
     *
     * The POST method is used to request that the origin server accept the entity enclosed in the request as a new
     * subordinate of the resource identified by the Request-URI in the Request-Line. POST is designed to allow a
     * uniform method to cover the following functions:
     *
     * - Annotation of existing resources;
     * - Posting a message to a bulletin board, newsgroup, mailing list, or similar group of articles;
     * - Providing a block of data, such as the result of submitting a form, to a data-handling process;
     * - Extending a database through an append operation.
     *
     * The actual function performed by the POST method is determined by the server and is usually dependent on the
     * Request-URI. The posted entity is subordinate to that URI in the same way that a file is subordinate to a
     * directory containing it, a news article is subordinate to a newsgroup to which it is posted, or a record is
     * subordinate to a database.
     *
     * The action performed by the POST method might not result in a resource that can be identified by a URI. In this
     * case, either 200 (OK) or 204 (No Content) is the appropriate response status, depending on whether or not the
     * response includes an entity that describes the result.
     *
     * If a resource has been created on the origin server, the response SHOULD be 201 (Created) and contain an entity
     * which describes the status of the request and refers to the new resource, and a Location header.
     *
     * Responses to this method are not cacheable, unless the response includes appropriate Cache-Control or Expires
     * header fields. However, the 303 (See Other) response can be used to direct the user agent to retrieve a
     * cacheable resource.
     *
     * @var string
     */
    const METHOD_POST = 'POST';

    /**
     * RequestInterface::METHOD_PUT
     *
     * The PUT method requests that the enclosed entity be stored under the supplied Request-URI. If the Request-URI
     * refers to an already existing resource, the enclosed entity SHOULD be considered as a modified version of the
     * one residing on the origin server. If the Request-URI does not point to an existing resource, and that URI is
     * capable of being defined as a new resource by the requesting user agent, the origin server can create the
     * resource with that URI. If a new resource is created, the origin server MUST inform the user agent via the 201
     * (Created) response. If an existing resource is modified, either the 200 (OK) or 204 (No Content) response codes
     * SHOULD be sent to indicate successful completion of the request. If the resource could not be created or
     * modified with the Request-URI, an appropriate error response SHOULD be given that reflects the nature of the
     * problem. The recipient of the entity MUST NOT ignore any Content-* (e.g. Content-Range) headers that it does not
     * understand or implement and MUST return a 501 (Not Implemented) response in such cases.
     *
     * If the request passes through a cache and the Request-URI identifies one or more currently cached entities,
     * those entries SHOULD be treated as stale. Responses to this method are not cacheable.
     *
     * The fundamental difference between the POST and PUT requests is reflected in the different meaning of the
     * Request-URI. The URI in a POST request identifies the resource that will handle the enclosed entity. That
     * resource might be a data-accepting process, a gateway to some other protocol, or a separate entity that accepts
     * annotations. In contrast, the URI in a PUT request identifies the entity enclosed with the request -- the user
     * agent knows what URI is intended and the server MUST NOT attempt to apply the request to some other resource. If
     * the server desires that the request be applied to a different URI, it MUST send a 301 (Moved Permanently) response; the user agent MAY then make its own decision regarding whether
     * or not to redirect the request.
     *
     * A single resource MAY be identified by many different URIs. For example, an article might have a URI for
     * identifying "the current version" which is separate from the URI identifying each particular version. In this
     * case, a PUT request on a general URI might result in several other URIs being defined by the origin server.
     *
     * HTTP/1.1 does not define how a PUT method affects the state of an origin server.
     *
     * PUT requests MUST obey the message transmission requirements set out in section 8.2.
     *
     * Unless otherwise specified for a particular entity-header, the entity-headers in the PUT request SHOULD be
     * applied to the resource created or modified by the PUT.
     *
     * @var string
     */
    const METHOD_PUT = 'PUT';

    /**
     * RequestInterface::METHOD_DELETE
     *
     * The DELETE method requests that the origin server delete the resource identified by the Request-URI. This method
     * MAY be overridden by human intervention (or other means) on the origin server. The client cannot be guaranteed
     * that the operation has been carried out, even if the status code returned from the origin server indicates that
     * the action has been completed successfully. However, the server SHOULD NOT indicate success unless, at the time
     * the response is given, it intends to delete the resource or move it to an inaccessible location.
     *
     * A successful response SHOULD be 200 (OK) if the response includes an entity describing the status, 202
     * (Accepted) if the action has not yet been enacted, or 204 (No Content) if the action has been enacted but the
     * response does not include an entity.
     *
     * If the request passes through a cache and the Request-URI identifies one or more currently cached entities,
     * those entries SHOULD be treated as stale. Responses to this method are not cacheable.
     *
     * @var string
     */
    const METHOD_DELETE = 'DELETE';

    /**
     * RequestInterface::METHOD_TRACE
     *
     * The TRACE method is used to invoke a remote, application-layer loop- back of the request message. The final
     * recipient of the request SHOULD reflect the message received back to the client as the entity-body of a 200 (OK)
     * response. The final recipient is either the origin server or the first proxy or gateway to receive a
     * Max-Forwards value of zero (0) in the request. A TRACE request MUST NOT include an entity.
     *
     * TRACE allows the client to see what is being received at the other end of the request chain and use that data
     * for testing or diagnostic information. The value of the Via header field (section 14.45) is of particular
     * interest, since it acts as a trace of the request chain. Use of the Max-Forwards header field allows the client
     * to limit the length of the request chain, which is useful for testing a chain of proxies forwarding messages in
     * an infinite loop.
     *
     * If the request is valid, the response SHOULD contain the entire request message in the entity-body, with a
     * Content-Type of "message/http". Responses to this method MUST NOT be cached.
     *
     * @var string
     */
    const METHOD_TRACE = 'TRACE';

    /**
     * RequestInterface::METHOD_CONNECT
     *
     * This specification reserves the method name CONNECT for use with a proxy that can dynamically
     * switch to being a tunnel (e.g. SSL tunneling).
     *
     * @var string
     */
    const METHOD_CONNECT = 'CONNECT';

    /**
     * RequestInterface::getRequestTarget
     *
     * Retrieves the message's request target.
     *
     * Retrieves the message's request-target either as it will appear (for
     * clients), as it appeared at request (for servers), or as it was
     * specified for the instance (see withRequestTarget()).
     *
     * In most cases, this will be the origin-form of the composed URI,
     * unless a value was provided to the concrete implementation (see
     * withRequestTarget() below).
     *
     * If no URI is available, and no request-target has been specifically
     * provided, this method MUST return the string "/".
     *
     * @return string
     */
    public function getRequestTarget();

    // ------------------------------------------------------------------------

    /**
     * RequestInterface::withRequestTarget
     *
     * Return an instance with the specific request-target.
     *
     * If the request needs a non-origin-form request-target — e.g., for
     * specifying an absolute-form, authority-form, or asterisk-form —
     * this method may be used to create an instance with the specified
     * request-target, verbatim.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request target.
     *
     * @see http://tools.ietf.org/html/rfc7230#section-5.3 (for the various
     *     request-target forms allowed in request messages)
     *
     * @param mixed $requestTarget
     *
     * @return static
     */
    public function withRequestTarget($requestTarget);

    // ------------------------------------------------------------------------

    /**
     * RequestInterface::getMethod
     *
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    public function getMethod();

    // ------------------------------------------------------------------------

    /**
     * RequestInterface::withMethod
     *
     * Return an instance with the provided HTTP method.
     *
     * While HTTP method names are typically all uppercase characters, HTTP
     * method names are case-sensitive and thus implementations SHOULD NOT
     * modify the given string.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request method.
     *
     * @param string $method Case-sensitive method.
     *
     * @return static
     * @throws \InvalidArgumentException for invalid HTTP methods.
     */
    public function withMethod($method);

    // ------------------------------------------------------------------------

    /**
     * RequestInterface::getUri
     *
     * Retrieves the URI instance.
     *
     * This method MUST return a UriInterface instance.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.3
     * @return UriInterface Returns a UriInterface instance
     *     representing the URI of the request.
     */
    public function getUri();

    // ------------------------------------------------------------------------

    /**
     * RequestInterface::withUri
     *
     * Returns an instance with the provided URI.
     *
     * This method MUST update the Host header of the returned request by
     * default if the URI contains a host component. If the URI does not
     * contain a host component, any pre-existing Host header MUST be carried
     * over to the returned request.
     *
     * You can opt-in to preserving the original state of the Host header by
     * setting `$preserveHost` to `true`. When `$preserveHost` is set to
     * `true`, this method interacts with the Host header in the following ways:
     *
     * - If the Host header is missing or empty, and the new URI contains
     *   a host component, this method MUST update the Host header in the returned
     *   request.
     * - If the Host header is missing or empty, and the new URI does not contain a
     *   host component, this method MUST NOT update the Host header in the returned
     *   request.
     * - If a Host header is present and non-empty, this method MUST NOT update
     *   the Host header in the returned request.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new UriInterface instance.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.3
     *
     * @param UriInterface $uri          New request URI to use.
     * @param bool         $preserveHost Preserve the original state of the Host header.
     *
     * @return static
     */
    public function withUri(UriInterface $uri, $preserveHost = false);
}