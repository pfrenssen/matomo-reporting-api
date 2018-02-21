<?php

namespace Piwik\ReportingApi;

use GuzzleHttp\Client;

/**
 * Default implementation of a query for the Piwik reporting API.
 */
class Query implements QueryInterface
{

    /**
     * An http client that encapsulates a GuzzleHttp client.
     *
     * @var \Piwik\ReportingApi\HttpClient
     */
    protected $httpClient;

    /**
     * Associative array of query parameters, keyed by parameter name.
     *
     * @var array
     */
    protected $parameters;

    /**
     * Constructs a new Query object.
     *
     * @param string $url
     *   The URL of the Piwik server.
     * @param \GuzzleHttp\Client $httpClient
     *   The Guzzle HTTP client.
     */
    public function __construct($url, Client $httpClient)
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new \InvalidArgumentException('Invalid URL.');
        }
        $this->httpClient = new HttpClient($httpClient);
        $this->httpClient->setUrl($url);
    }

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters)
    {
        foreach ($parameters as $name => $value) {
            $this->setParameter($name, $value);
        }

        return $this;    }

    /**
     * {@inheritdoc}
     */
    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameter($name)
    {
        if (!array_key_exists($name, $this->parameters)) {
            throw new \InvalidArgumentException("Parameter '$name' is not set.");
        }
        return $this->parameters[$name];
    }

    /**
     * Returns the http client.
     *
     * @return \Piwik\ReportingApi\HttpClient
     *   The http client.
     */
    public function getHttpClient() {
        return $this->httpClient;
    }

    /**
     * Prepares the query for execution.
     */
    protected function prepareExecute()
    {
        // Set the format to JSON.
        $this->setParameter('format', 'json');

        // Use the reporting API.
        $this->setParameter('module', 'API');
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->prepareExecute();
        $response = $this->httpClient->setRequestParams($this->parameters)->execute();
        return new QueryResult($response);
    }
}
