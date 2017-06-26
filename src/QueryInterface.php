<?php

namespace Piwik\ReportingApi;

/**
 * Interface for classes that query the Piwik reporting API.
 */
interface QueryInterface
{

    /**
     * Sets a range of query parameters.
     *
     * @param array $parameters
     *   An associative array of query parameters, keyed by parameter name.
     *
     * @return $this
     *   The updated query object.
     */
    public function setParameters(array $parameters);

    /**
     * Sets a query parameter.
     *
     * @param string $name
     *   The name of the parameter to set.
     * @param mixed $value
     *   The value to set.
     *
     * @return $this
     *   The updated query object.
     */
    public function setParameter($name, $value);

    /**
     * Executes the query.
     *
     * @return \Piwik\ReportingApi\QueryResult
     *   The query result.
     */
    public function execute();
}
