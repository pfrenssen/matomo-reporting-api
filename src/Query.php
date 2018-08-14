<?php

namespace Matomo\ReportingApi;

/**
 * Default implementation of a query for the Matomo reporting API.
 */
class Query implements QueryInterface
{

    /**
     * An HTTP client that encapsulates a GuzzleHttp client.
     *
     * @var \Matomo\ReportingApi\HttpClient
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
     *   The URL of the Matomo server.
     * @param \Matomo\ReportingApi\HttpClient $httpClient
     *   The HTTP client wrapper.
     */
    public function __construct($url, HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
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
        $response = $this->httpClient->setRequestParameters($this->parameters)->sendRequest();
        return new QueryResult($response);
    }
}
