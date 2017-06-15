<?php

namespace Piwik\ReportingApi;

use GuzzleHttp\Client;

/**
 * Default implementation of a query for the Piwik reporting API.
 */
class Query implements QueryInterface
{

    /**
     * The Guzzle HTTP client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * Associative array of query parameters, keyed by parameter name.
     *
     * @var array
     */
    protected $parameters;

    /**
     * The URL of the Piwik server.
     *
     * @var string
     */
    protected $url;

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
        $this->url = $url;
        $this->httpClient = $httpClient;
    }

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters)
    {
        foreach ($parameters as $name => $value) {
            $this->setParameter($name, $value);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;

        return $this;
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
        $result = $this->httpClient->get($this->url, ['query' => $this->parameters]);
        return json_decode($result->getBody());
    }
}
