<?php

namespace Matomo\ReportingApi;

use GuzzleHttp\Psr7\Response;

/**
 * Contains the result of a query to the Matomo Reporting API.
 */
class QueryResult
{

    /**
     * The raw HTTP response.
     *
     * @var \GuzzleHttp\Psr7\Response
     */
    protected $response;

    /**
     * The decoded response.
     *
     * @var mixed
     */
    protected $decodedResponse;

    /**
     * Constructs a QueryResult object.
     *
     * @param \GuzzleHttp\Psr7\Response $response
     *   The HTTP response from the Matomo server that contains the query result.
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Returns the decoded response that was returned by the Matomo server.
     *
     * @return mixed
     *   The response. Can be an object, or an array of objects in case multiple
     *   results are returned.
     */
    public function getResponse()
    {
        if (empty($this->decodedResponse)) {
            $this->decodedResponse = json_decode($this->response->getBody());
        }
        return $this->decodedResponse;
    }

    /**
     * Returns the raw HTTP response object.
     *
     * Use this if you need to inspect the HTTP headers, or other data.
     *
     * @return \GuzzleHttp\Psr7\Response
     *   The HTTP response.
     */
    public function getRawResponse()
    {
        return $this->response;
    }

    /**
     * Returns whether or not an error occurred in the request.
     *
     * @return bool
     *   TRUE if an error occurred.
     */
    public function hasError()
    {
        if ($this->getRawResponse()->getStatusCode() != 200) {
            return true;
        }
        // If an error occurs the Matomo server still returns a 200 OK response,
        // but the body of the response will contain the string "error" in the
        // "result" parameter.
        // @see https://github.com/matomo/matomo/issues/7293
        return !$this->isObject() || $this->parameterExists('result') && $this->get('result') === 'error';
    }

    /**
     * Returns the error message if one is available.
     *
     * @return string|null
     *   The error message, or NULL if no error message is available.
     */
    public function getErrorMessage()
    {
        if ($this->hasError() && $this->parameterExists('message')) {
            return $this->get('message');
        }
    }

    /**
     * Returns the value that corresponds with the given parameter name.
     *
     * @param string $name
     *   The parameter name for which to return the value.
     *
     * @return mixed
     *   The value.
     *
     * @throws \InvalidArgumentException
     *   Thrown when no parameter with the given name exists, or if the response
     *   is not an object.
     */
    public function get($name)
    {
        if (!$this->isObject()) {
            throw new \InvalidArgumentException("Cannot retrieve parameter '$name', the response is not an object.");
        }

        if ($this->parameterExists($name)) {
            return $this->getResponse()->$name;
        }

        throw new \InvalidArgumentException("Parameter '$name' does not exist.");
    }

    /**
     * Checks whether the parameter with the given name exists.
     *
     * @param string $name
     *   The name of the parameter to check.
     *
     * @return bool
     *   TRUE if the parameter exists.
     */
    public function parameterExists($name)
    {
        if (!$this->isObject()) {
            throw new \InvalidArgumentException("Cannot check if '$name' exists, the response is not an object.");
        }
        return property_exists($this->getResponse(), $name);
    }

    /**
     * Checks whether an object was returned.
     *
     * @return bool
     *   TRUE if the response is an object, FALSE if it is something else.
     */
    public function isObject()
    {
        return is_object($this->getResponse());
    }

    /**
     * Checks whether an array was returned.
     *
     * @return bool
     *   TRUE if the response is an array, FALSE if it is something else.
     */
    public function isArray()
    {
        return is_array($this->getResponse());
    }

    /**
     * Returns the number of results that are present in the response.
     *
     * @return int
     *   The number of results.
     *
     * @throws \DomainException
     *   Thrown when the response that was returned by Matomo was not an array.
     */
    public function getResultCount()
    {
        if (!$this->isArray()) {
            throw new \DomainException('Cannot get result count, the response is not an array.');
        }
        return count($this->getResponse());
    }
}
