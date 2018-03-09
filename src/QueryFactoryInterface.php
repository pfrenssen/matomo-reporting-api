<?php

namespace Piwik\ReportingApi;

/**
 * Interface for classes that provide Piwik reporting API query objects.
 */
interface QueryFactoryInterface
{

    /**
     * Returns a new QueryFactory using default settings.
     *
     * @param string $url
     *   The URL of the Piwik server.
     *
     * @return \Piwik\ReportingApi\QueryFactoryInterface
     *   The new QueryFactory object.
     */
    public static function create($url);

    /**
     * Sets the URL of the Piwik server.
     *
     * @param string $url
     *   The URL of the Piwik server.
     *
     * @return $this
     *   The updated query factory object.
     */
    public function setUrl($url);

    /**
     * Sets a default parameter.
     *
     * @param string $name
     *   The name of the parameter to set.
     * @param mixed $value
     *   The value to set
     *
     * @return $this
     *   The updated query factory object.
     */
    public function set($name, $value);

    /**
     * Returns whether or not the default parameter with the given name is set.
     *
     * @return bool
     *   TRUE if the parameter has been set, FALSE otherwise.
     */
    public function has($name);

    /**
     * Removes the default parameter with the given name.
     *
     * @param string $name
     *   The name of the parameter to unset.
     *
     * @return $this
     *   The updated query factory object.
     */
    public function remove($name);

    /**
     * Returns a query object for the given Piwik API method.
     *
     * @param string $method
     *   The name of the method for which to return a query object, in the format
     *   'ModuleName.methodName'.
     *
     * @return \Piwik\ReportingApi\Query
     *   The query object.
     */
    public function getQuery($method);

    /**
     * Returns the HTTP client wrapper.
     *
     * @return \Piwik\ReportingApi\HttpClient
     *   The HTTP client wrapper.
     */
    public function getHttpClient();
}
