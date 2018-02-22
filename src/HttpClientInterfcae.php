<?php

namespace Piwik\ReportingApi;

interface HttpClientInterfcae
{
    /**
     * Sets the request parameters.
     *
     * @param array $requestParams
     *   The request parameters array.
     *
     * @return \Piwik\ReportingApi\HttpClient
     *   The object itself for chain calls.
     */
    public function setRequestParams(array $requestParams);

    /**
     * Returns the request parameters.
     *
     * @return array
     *   The request parameters.
     */
    public function getRequestParams();

    /**
     * Returns the request method.
     *
     * @return string
     *   The request method.
     */
    public function getMethod();

    /**
     * Sets the request method.
     *
     * @param string $method
     *   The request method.
     *
     * @return \Piwik\ReportingApi\HttpClient
     *   The object itself for chain calls.
     *
     * @throws \InvalidArgumentException
     *   Thrown if the method passed is not supported.
     */
    public function setMethod($method);

    /**
     * Returns the request url.
     *
     * @return string
     *   The request url.
     */
    public function getUrl();

    /**
     * Sets the url of the request.
     *
     * @param string $url
     *   The url of the request.
     *
     * @return \Piwik\ReportingApi\HttpClient
     *   The object itself for chain calls.
     *
     * @throws \InvalidArgumentException
     *   Thrown when the url passed is not a valid url.
     */
    public function setUrl($url);

    /**
     * Executes the request and returns the results.
     *
     * @return \GuzzleHttp\Psr7\Response
     *   A response that the request generated.
     *
     * @throws \Exception
     *   Thrown when the url is not set yet.
     */
    public function execute();
}