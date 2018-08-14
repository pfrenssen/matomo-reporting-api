<?php

namespace Matomo\ReportingApi;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * Interface for classes that create PSR7 compatible request objects.
 */
interface RequestFactoryInterface
{
    /**
     * @param string $method
     *   The HTTP method to use.
     * @param string|UriInterface $uri
     *   The URI to use.
     * @param array $headers
     *   Optional request headers.
     * @param string|null|resource|StreamInterface $body
     *   Optional request body.
     * @param string $version
     *   Optional protocol version.
     *
     * @return RequestInterface
     */
    public function getRequest($method, $uri, array $headers = [], $body = null, $version = '1.1');
}
