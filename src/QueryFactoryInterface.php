<?php

namespace Matomo\ReportingApi;

/**
 * Interface for classes that provide Matomo reporting API query objects.
 */
interface QueryFactoryInterface
{

    /**
     * Returns a new QueryFactory using default settings.
     *
     * @param string $url
     *   The URL of the Matomo server.
     *
     * @return \Matomo\ReportingApi\QueryFactoryInterface
     *   The new QueryFactory object.
     */
    public static function create($url);

    /**
     * Sets the URL of the Matomo server.
     *
     * @param string $url
     *   The URL of the Matomo server.
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
     * Returns a query object for the given Matomo API method.
     *
     * @param string $method
     *   The name of the method for which to return a query object, in the format
     *   'ModuleName.methodName'.
     *
     * @return \Matomo\ReportingApi\Query
     *   The query object.
     */
    public function getQuery($method);

    /**
     * Returns the HTTP client wrapper.
     *
     * @return \Matomo\ReportingApi\HttpClient
     *   The HTTP client wrapper.
     */
    public function getHttpClient();
}
