<?php

namespace Matomo\ReportingApi;

use GuzzleHttp\Psr7\Request;

/**
 * Default implementation of a class that creates a PSR7 request object.
 */
class RequestFactory implements RequestFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRequest($method, $uri, array $headers = [], $body = null, $version = '1.1')
    {
        return new Request($method, $uri, $headers, $body, $version);
    }
}
