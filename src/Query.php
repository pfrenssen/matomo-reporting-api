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
    public function execute($method = 'GET')
    {
        $method = strtoupper($method);
        if (!in_array($method, ['GET', 'POST'])) {
          throw new \InvalidArgumentException("Only 'GET' and 'POST' requests are allowed.");
        }

        $this->prepareExecute();
        $response = $method === 'GET' ?
          $this->httpClient->get($this->url, ['query' => $this->parameters]) :
          $this->httpClient->post($this->url, ['form_params' => $this->parameters]);
        return new QueryResult($response);
    }

    /**
     * {@inheritdoc}
     */
    public function get() {
      return $this->execute('GET');
    }

    /**
     * {@inheritdoc}
     */
    public function post() {
      return $this->execute('POST');
    }
}
