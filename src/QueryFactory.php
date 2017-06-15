<?php

namespace Piwik\ReportingApi;

use GuzzleHttp\Client;

/**
 * Factory for easy instantiation of Piwik reporting API query objects.
 */
class QueryFactory implements QueryFactoryInterface
{

    /**
     * Associative array of default parameters, keyed by parameter name.
     *
     * @var array
     */
    protected $defaultParameters;

    /**
     * The URL of the Piwik server.
     *
     * @var string
     */
    protected $url;

    /**
     * Constructs a new QueryFactory.
     *
     * @param string $url
     *   The URL of the Piwik server.
     * @param \GuzzleHttp\Client $httpClient
     *   The Guzzle HTTP client.
     */
    public function __construct($url, Client $httpClient)
    {
        $this->url = $url;
        $this->httpClient = $httpClient;
    }

    /**
     * Returns a new QueryFactory using default settings.
     *
     * @param string $url
     *   The URL of the Piwik server.
     *
     * @return \Piwik\ReportingApi\QueryFactoryInterface
     *   The new QueryFactory object.
     */
    public static function create($url)
    {
        return new static($url, new Client());
    }

    /**
     * Sets the URL of the Piwik server.
     *
     * @param string $url
     *   The URL of the Piwik server.
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Sets a default parameter.
     *
     * @param string $name
     *   The name of the parameter to set.
     * @param mixed $value
     *   The value to set
     */
    public function set($name, $value)
    {
        $this->defaultParameters[$name] = $value;
    }

    /**
     * Returns whether or not the default parameter with the given name is set.
     *
     * @return bool
     *   TRUE if the parameter has been set, FALSE otherwise.
     */
    public function has($name)
    {
        return array_key_exists($name, $this->defaultParameters);
    }

    /**
     * Unsets the default parameter with the given name.
     */
    public function unset($name)
    {
        unset($this->defaultParameters[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery($method)
    {
        $query = new Query($this->url, $this->httpClient);
        if (!empty($this->defaultParameters)) {
            $query->setParameters($this->defaultParameters);
        }
        $query->setParameter('method', $method);

        return $query;
    }
}
