<?php

namespace Piwik\ReportingApi;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

/**
 * A wrapper class that provides request options for the http client.
 */
class HttpClient implements HttpClientInterface
{

    /**
     * The Guzzle HTTP client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * The parameters to pass to the request.
     *
     * @var array
     */
    protected $requestParams = array();

    /**
     * The request method.
     *
     * @var string
     */
    protected $method = 'GET';

    /**
     * The request url.
     *
     * @var string;
     */
    protected $url;

    /**
     * Constructs a new HttpClient object.
     *
     * @param \GuzzleHttp\Client $httpClient
     *   The Guzzle HTTP client.
     */
    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * {@inheritdoc}
     */
    public function setRequestParams(array $requestParams)
    {
        $this->requestParams = $requestParams;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestParams()
    {
        return $this->requestParams;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * {@inheritdoc}
     */
    public function setMethod($method)
    {
        // Currently, only GET and POST requests are supported.
        if (!in_array($method, array('GET', 'POST'))) {
            throw new \InvalidArgumentException(
              'Only GET and POST requests are allowed.'
            );
        }
        $this->method = $method;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * {@inheritdoc}
     */
    public function setUrl($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new \InvalidArgumentException('Invalid URL.');
        }

        $this->url = $url;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if (empty($this->getUrl())) {
            throw new \Exception('Request url is not set.');
        }
        $request = new Request($this->getMethod(), $this->getUrl());
        $param_type = $this->method === 'GET' ? 'query' : 'form_params';

        return $this->httpClient->send(
          $request,
          array($param_type => $this->getRequestParams())
        );
    }
}
